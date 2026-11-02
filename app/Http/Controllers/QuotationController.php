<?php

namespace App\Http\Controllers;

use App\Models\FpoCollectiveListing;
use App\Models\ProduceLot;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderStatusLog;
use App\Models\Quotation;
use App\Models\Rfq;
use App\Models\User;
use App\Notifications\QuotationReceivedNotification;
use App\Notifications\QuotationStatusNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class QuotationController extends Controller
{
    /**
     * Resolve active user.
     */
    protected function getActiveUser(): User
    {
        $user = Auth::user();
        if ($user) {
            return $user;
        }

        return User::where('role', 'farmer')->first() ?? User::first();
    }

    /**
     * Show form for seller to submit quotation on an RFQ.
     */
    public function create(Rfq $rfq)
    {
        $user = $this->getActiveUser();
        $this->authorize('submitQuote', $rfq);

        // Check if seller already submitted a quotation
        $existing = $rfq->quotations()->where('seller_user_id', $user->id)->first();
        if ($existing) {
            return redirect()->route('quotations.show', $existing->id)
                ->with('info', "You have already submitted Quotation #{$existing->quotation_number} for this RFQ.");
        }

        $rfq->load(['buyer', 'produceLot', 'product', 'collectiveListing']);

        return view('pages.quotation-create', compact('rfq', 'user'));
    }

    /**
     * Persist quotation submitted by seller.
     */
    public function store(Request $request, Rfq $rfq)
    {
        $user = $this->getActiveUser();
        $this->authorize('submitQuote', $rfq);

        $validated = $request->validate([
            'quoted_quantity' => 'required|numeric|min:0.1|max:10000000',
            'unit' => 'required|string|max:30',
            'unit_price' => 'required|numeric|min:0.01|max:1000000',
            'additional_charges' => 'nullable|numeric|min:0|max:1000000',
            'tax_amount' => 'nullable|numeric|min:0|max:1000000',
            'estimated_delivery_date' => 'required|date|after_or_equal:today',
            'valid_until' => 'required|date|after_or_equal:today',
            'quality_notes' => 'nullable|string|max:2000',
            'terms_and_conditions' => 'nullable|string|max:2000',
        ]);

        $quotedQty = (float) $validated['quoted_quantity'];
        $unitPrice = (float) $validated['unit_price'];
        $extraCharges = (float) ($validated['additional_charges'] ?? 0);
        $tax = (float) ($validated['tax_amount'] ?? 0);

        // STRICT SERVER-SIDE MONETARY CALCULATION (Never trust client calculations!)
        $subtotal = round($quotedQty * $unitPrice, 2);
        $totalAmount = round($subtotal + $extraCharges + $tax, 2);

        // Determine seller type & org
        $sellerType = 'farmer';
        $supplierOrgId = null;
        $fpoOrgId = null;

        if ($user->isSupplier()) {
            $sellerType = 'supplier';
            $supplierOrgId = $user->primarySupplierOrganization()?->id;
        } elseif ($user->isFpo()) {
            $sellerType = 'fpo';
            $fpoOrgId = $user->primaryFpoOrganization()?->id;
        }

        $quotationNumber = 'QT-'.date('Ymd').'-'.rand(1000, 9999);

        $quotation = DB::transaction(function () use (
            $rfq,
            $user,
            $sellerType,
            $supplierOrgId,
            $fpoOrgId,
            $validated,
            $quotedQty,
            $unitPrice,
            $extraCharges,
            $tax,
            $totalAmount,
            $quotationNumber
        ) {
            $quote = Quotation::create([
                'quotation_number' => $quotationNumber,
                'rfq_id' => $rfq->id,
                'seller_user_id' => $user->id,
                'seller_type' => $sellerType,
                'supplier_organization_id' => $supplierOrgId,
                'fpo_organization_id' => $fpoOrgId,
                'produce_lot_id' => $rfq->produce_lot_id,
                'product_id' => $rfq->product_id,
                'fpo_collective_listing_id' => $rfq->fpo_collective_listing_id,
                'quoted_quantity' => $quotedQty,
                'unit' => $validated['unit'],
                'unit_price' => $unitPrice,
                'additional_charges' => $extraCharges,
                'tax_amount' => $tax,
                'total_amount' => $totalAmount,
                'estimated_delivery_date' => $validated['estimated_delivery_date'],
                'valid_until' => $validated['valid_until'],
                'status' => Quotation::STATUS_PENDING,
                'quality_notes' => $validated['quality_notes'] ?? null,
                'terms_and_conditions' => $validated['terms_and_conditions'] ?? null,
            ]);

            // Update RFQ status to quotations_received
            if ($rfq->status === Rfq::STATUS_OPEN) {
                $rfq->update(['status' => Rfq::STATUS_QUOTATIONS_RECEIVED]);
            }

            // Update invitation status if exists
            $rfq->invitations()
                ->where(function ($q) use ($user, $supplierOrgId, $fpoOrgId) {
                    $q->where('recipient_user_id', $user->id)
                        ->orWhere('supplier_organization_id', $supplierOrgId)
                        ->orWhere('fpo_organization_id', $fpoOrgId);
                })
                ->update(['status' => 'quoted']);

            return $quote;
        });

        // Notify buyer of new quotation
        if ($rfq->buyer) {
            $rfq->buyer->notify(new QuotationReceivedNotification($quotation));
        }

        return redirect()->route('quotations.show', $quotation->id)
            ->with('success', "Quotation #{$quotation->quotation_number} successfully submitted for RFQ #{$rfq->rfq_number}.");
    }

    /**
     * Show quotation details.
     */
    public function show(Quotation $quotation)
    {
        $user = $this->getActiveUser();
        $this->authorize('view', $quotation);

        $quotation->load([
            'rfq.buyer',
            'sellerUser',
            'supplierOrganization',
            'fpoOrganization',
            'produceLot.crop',
            'product',
            'collectiveListing',
            'purchaseOrder',
        ]);

        return view('pages.quotation-detail', compact('quotation', 'user'));
    }

    /**
     * Withdraw quotation by seller.
     */
    public function withdraw(Quotation $quotation)
    {
        $user = $this->getActiveUser();
        $this->authorize('update', $quotation);

        $quotation->update(['status' => Quotation::STATUS_WITHDRAWN]);

        return redirect()->route('quotations.show', $quotation->id)
            ->with('success', "Quotation #{$quotation->quotation_number} has been withdrawn.");
    }

    /**
     * Buyer accepts quotation: locks RFQ, commits inventory, and creates Purchase Order.
     */
    public function accept(Quotation $quotation)
    {
        $user = $this->getActiveUser();
        $this->authorize('accept', $quotation);

        $purchaseOrder = DB::transaction(function () use ($quotation, $user) {
            // 1. Concurrency-safe row locking
            $lockedQuote = Quotation::where('id', $quotation->id)->lockForUpdate()->firstOrFail();
            $lockedRfq = Rfq::where('id', $quotation->rfq_id)->lockForUpdate()->firstOrFail();

            if (! $lockedQuote->isAcceptable()) {
                throw ValidationException::withMessages([
                    'quotation' => 'This quotation is either no longer pending or has expired.',
                ]);
            }

            // 1. Concurrency-safe atomic transition for RFQ and winning quotation
            $rfqAwarded = Rfq::where('id', $lockedRfq->id)
                ->where('status', '!=', Rfq::STATUS_ACCEPTED)
                ->update(['status' => Rfq::STATUS_ACCEPTED]);

            if ($rfqAwarded === 0) {
                throw ValidationException::withMessages([
                    'quotation' => 'This RFQ has already been awarded and accepted.',
                ]);
            }

            $quoteAwarded = Quotation::where('id', $lockedQuote->id)
                ->where('status', Quotation::STATUS_PENDING)
                ->update(['status' => Quotation::STATUS_ACCEPTED]);

            if ($quoteAwarded === 0) {
                throw ValidationException::withMessages([
                    'quotation' => 'This quotation is either no longer pending or has expired.',
                ]);
            }

            $orderQty = (float) $lockedQuote->quoted_quantity;

            // 2. Stock Commitment & Concurrency-Safe Stock Validation
            if ($lockedQuote->produce_lot_id) {
                $lockedLot = ProduceLot::where('id', $lockedQuote->produce_lot_id)->lockForUpdate()->firstOrFail();
                if ($lockedLot->available_quantity_kg < $orderQty) {
                    throw ValidationException::withMessages([
                        'quotation' => "Produce Lot #{$lockedLot->lot_number} does not have sufficient available stock ({$lockedLot->available_quantity_kg} kg available, {$orderQty} kg required).",
                    ]);
                }

                $lockedLot->available_quantity_kg -= $orderQty;
                $lockedLot->reserved_quantity_kg += $orderQty;
                if ($lockedLot->available_quantity_kg <= 0.001) {
                    $lockedLot->available_quantity_kg = 0;
                    $lockedLot->status = ProduceLot::STATUS_SOLD_OUT;
                } else {
                    $lockedLot->status = ProduceLot::STATUS_PARTIALLY_SOLD;
                }
                $lockedLot->save();
            } elseif ($lockedQuote->product_id) {
                $lockedProduct = Product::where('id', $lockedQuote->product_id)->lockForUpdate()->firstOrFail();
                if ($lockedProduct->stock_quantity < $orderQty) {
                    throw ValidationException::withMessages([
                        'quotation' => "Product '{$lockedProduct->name}' does not have sufficient inventory ({$lockedProduct->stock_quantity} available, {$orderQty} requested).",
                    ]);
                }

                $lockedProduct->stock_quantity -= $orderQty;
                if ($lockedProduct->stock_quantity <= 0) {
                    $lockedProduct->stock_quantity = 0;
                    $lockedProduct->is_available = false;
                }
                $lockedProduct->save();
            } elseif ($lockedQuote->fpo_collective_listing_id) {
                $lockedFpoListing = FpoCollectiveListing::where('id', $lockedQuote->fpo_collective_listing_id)->lockForUpdate()->firstOrFail();
                if ($lockedFpoListing->available_quantity_kg < $orderQty) {
                    throw ValidationException::withMessages([
                        'quotation' => "FPO Bulk Listing #{$lockedFpoListing->listing_number} does not have sufficient available volume ({$lockedFpoListing->available_quantity_kg} kg available, {$orderQty} kg required).",
                    ]);
                }

                $lockedFpoListing->available_quantity_kg -= $orderQty;
                $lockedFpoListing->reserved_quantity_kg += $orderQty;
                if ($lockedFpoListing->available_quantity_kg <= 0.001) {
                    $lockedFpoListing->available_quantity_kg = 0;
                    $lockedFpoListing->status = FpoCollectiveListing::STATUS_SOLD_OUT;
                } else {
                    $lockedFpoListing->status = FpoCollectiveListing::STATUS_PARTIALLY_SOLD;
                }
                $lockedFpoListing->save();
            }

            // 3. Mark all other competing pending quotations on this RFQ as Declined
            Quotation::where('rfq_id', $lockedRfq->id)
                ->where('id', '!=', $lockedQuote->id)
                ->where('status', Quotation::STATUS_PENDING)
                ->update(['status' => Quotation::STATUS_DECLINED]);

            // 6. Generate Persisted Purchase Order
            $poNumber = 'PO-'.date('Ymd').'-'.rand(1000, 9999);
            $subtotal = round($orderQty * (float) $lockedQuote->unit_price, 2);

            $po = PurchaseOrder::create([
                'po_number' => $poNumber,
                'quotation_id' => $lockedQuote->id,
                'rfq_id' => $lockedRfq->id,
                'buyer_id' => $lockedRfq->buyer_id,
                'seller_user_id' => $lockedQuote->seller_user_id,
                'seller_type' => $lockedQuote->seller_type,
                'supplier_organization_id' => $lockedQuote->supplier_organization_id,
                'fpo_organization_id' => $lockedQuote->fpo_organization_id,
                'item_type' => $lockedRfq->item_type,
                'produce_lot_id' => $lockedQuote->produce_lot_id,
                'product_id' => $lockedQuote->product_id,
                'fpo_collective_listing_id' => $lockedQuote->fpo_collective_listing_id,
                'item_description' => $lockedRfq->title,
                'quantity' => $orderQty,
                'unit' => $lockedQuote->unit,
                'unit_price' => $lockedQuote->unit_price,
                'subtotal' => $subtotal,
                'additional_charges' => $lockedQuote->additional_charges,
                'tax_amount' => $lockedQuote->tax_amount,
                'total_amount' => $lockedQuote->total_amount,
                'delivery_address' => $lockedRfq->delivery_address ?: "{$lockedRfq->delivery_district}, Gujarat",
                'delivery_district' => $lockedRfq->delivery_district,
                'estimated_delivery_date' => $lockedQuote->estimated_delivery_date,
                'status' => PurchaseOrder::STATUS_CONFIRMED,
                'payment_status' => PurchaseOrder::PAYMENT_UNPAID,
                'payment_terms' => 'Standard Commercial Terms (Payment due within 15 days of verified delivery)',
                'notes' => "Generated from accepted Quotation #{$lockedQuote->quotation_number} under RFQ #{$lockedRfq->rfq_number}.",
            ]);

            // Audit log
            PurchaseOrderStatusLog::create([
                'purchase_order_id' => $po->id,
                'from_status' => 'created',
                'to_status' => PurchaseOrder::STATUS_CONFIRMED,
                'actor_id' => $user->id,
                'notes' => "Purchase order created upon accepting Quotation #{$lockedQuote->quotation_number}.",
            ]);

            return $po;
        });

        // Notifications
        if ($quotation->sellerUser) {
            $quotation->sellerUser->notify(new QuotationStatusNotification($quotation, 'accepted'));
        }

        // Notify competing sellers whose quotes were declined
        $declinedQuotes = Quotation::where('rfq_id', $quotation->rfq_id)
            ->where('id', '!=', $quotation->id)
            ->where('status', Quotation::STATUS_DECLINED)
            ->get();

        foreach ($declinedQuotes as $declinedQuote) {
            if ($declinedQuote->sellerUser) {
                $declinedQuote->sellerUser->notify(new QuotationStatusNotification($declinedQuote, 'declined'));
            }
        }

        return redirect()->route('orders.show', $purchaseOrder->id)
            ->with('success', "Quotation #{$quotation->quotation_number} accepted! Purchase Order #{$purchaseOrder->po_number} has been generated.");
    }
}
