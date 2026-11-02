<?php

namespace App\Http\Controllers;

use App\Models\FpoCollectiveListing;
use App\Models\FpoOrganization;
use App\Models\ProcurementRequirement;
use App\Models\ProduceLot;
use App\Models\Product;
use App\Models\Rfq;
use App\Models\RfqInvitation;
use App\Models\SupplierOrganization;
use App\Models\User;
use App\Notifications\RfqCreatedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RfqController extends Controller
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

        // Demo fallback
        return User::where('role', 'buyer')->first() ?? User::first();
    }

    /**
     * Display RFQs list for either Buyer (created) or Seller (invited).
     */
    public function index(Request $request)
    {
        $user = $this->getActiveUser();

        if ($user->isBuyer() || $user->isAdmin()) {
            $query = Rfq::with(['requirement', 'quotations', 'invitations'])
                ->where('buyer_id', $user->id)
                ->orderBy('id', 'desc');

            if ($request->filled('status')) {
                $query->where('status', $request->input('status'));
            }

            $rfqs = $query->paginate(10)->withQueryString();

            $stats = [
                'total' => Rfq::where('buyer_id', $user->id)->count(),
                'open' => Rfq::where('buyer_id', $user->id)->whereIn('status', [Rfq::STATUS_OPEN, Rfq::STATUS_QUOTATIONS_RECEIVED])->count(),
                'accepted' => Rfq::where('buyer_id', $user->id)->where('status', Rfq::STATUS_ACCEPTED)->count(),
            ];

            return view('pages.rfq-buyer-index', compact('rfqs', 'stats', 'user'));
        }

        // Seller Inbox
        $supplierOrgIds = $user->supplierOrganizations()->pluck('supplier_organizations.id')->toArray();
        $fpoOrgIds = $user->fpoOrganizations()->pluck('fpo_organizations.id')->toArray();

        $query = Rfq::with(['buyer', 'quotations'])
            ->where(function ($q) use ($user, $supplierOrgIds, $fpoOrgIds) {
                $q->whereHas('invitations', function ($sub) use ($user, $supplierOrgIds, $fpoOrgIds) {
                    $sub->where('recipient_user_id', $user->id)
                        ->orWhereIn('supplier_organization_id', $supplierOrgIds)
                        ->orWhereIn('fpo_organization_id', $fpoOrgIds);
                })
                    ->orWhere(function ($openQ) use ($user) {
                        if ($user->isFarmer()) {
                            $openQ->where('item_type', 'produce')->where('status', Rfq::STATUS_OPEN);
                        } elseif ($user->isSupplier()) {
                            $openQ->where('item_type', 'supplier_product')->where('status', Rfq::STATUS_OPEN);
                        } elseif ($user->isFpo()) {
                            $openQ->whereIn('item_type', ['produce', 'collective_produce'])->where('status', Rfq::STATUS_OPEN);
                        }
                    });
            })
            ->orderBy('id', 'desc');

        $rfqs = $query->paginate(10)->withQueryString();

        return view('pages.rfq-seller-index', compact('rfqs', 'user'));
    }

    /**
     * Show form to create new RFQ.
     */
    public function create(Request $request)
    {
        $user = $this->getActiveUser();

        $requirement = null;
        if ($request->filled('requirement_id')) {
            $requirement = ProcurementRequirement::find($request->input('requirement_id'));
        }

        $produceLot = null;
        if ($request->filled('produce_lot_id')) {
            $produceLot = ProduceLot::with('crop')->find($request->input('produce_lot_id'));
        }

        $product = null;
        if ($request->filled('product_id')) {
            $product = Product::with('category')->find($request->input('product_id'));
        }

        $collectiveListing = null;
        if ($request->filled('collective_listing_id')) {
            $collectiveListing = FpoCollectiveListing::find($request->input('collective_listing_id'));
        }

        $districts = ['Rajkot', 'Anand', 'Banaskantha', 'Surat', 'Mehsana', 'Junagadh', 'Ahmedabad', 'Kheda', 'Vadodara', 'Amreli', 'Bhavnagar', 'Patan'];

        // Available sellers for invitation
        $availableFarmers = User::where('role', 'farmer')->where('status', 'active')->take(10)->get();
        $availableSuppliers = SupplierOrganization::where('verification_status', SupplierOrganization::STATUS_APPROVED)->take(10)->get();
        $availableFpos = FpoOrganization::where('verification_status', FpoOrganization::STATUS_APPROVED)->take(10)->get();

        return view('pages.rfq-create', compact(
            'user',
            'requirement',
            'produceLot',
            'product',
            'collectiveListing',
            'districts',
            'availableFarmers',
            'availableSuppliers',
            'availableFpos'
        ));
    }

    /**
     * Store and dispatch new RFQ.
     */
    public function store(Request $request)
    {
        $user = $this->getActiveUser();

        $validated = $request->validate([
            'title' => 'required|string|max:200',
            'item_type' => 'required|in:produce,supplier_product,collective_produce',
            'quantity' => 'required|numeric|min:0.1|max:10000000',
            'unit' => 'required|string|max:30',
            'target_price_per_unit' => 'nullable|numeric|min:0',
            'delivery_district' => 'required|string|max:100',
            'delivery_address' => 'nullable|string|max:300',
            'target_delivery_date' => 'required|date|after_or_equal:today',
            'specifications' => 'nullable|string|max:2000',
            'notes' => 'nullable|string|max:1000',
            'procurement_requirement_id' => 'nullable|exists:procurement_requirements,id',
            'produce_lot_id' => 'nullable|exists:produce_lots,id',
            'product_id' => 'nullable|exists:products,id',
            'fpo_collective_listing_id' => 'nullable|exists:fpo_collective_listings,id',
            'invited_sellers' => 'nullable|array',
            'invited_sellers.*' => 'string',
        ]);

        $rfqNumber = 'RFQ-'.date('Ymd').'-'.rand(1000, 9999);

        $rfq = DB::transaction(function () use ($user, $validated, $rfqNumber) {
            $rfq = Rfq::create([
                'rfq_number' => $rfqNumber,
                'buyer_id' => $user->id,
                'procurement_requirement_id' => $validated['procurement_requirement_id'] ?? null,
                'item_type' => $validated['item_type'],
                'produce_lot_id' => $validated['produce_lot_id'] ?? null,
                'product_id' => $validated['product_id'] ?? null,
                'fpo_collective_listing_id' => $validated['fpo_collective_listing_id'] ?? null,
                'title' => $validated['title'],
                'quantity' => $validated['quantity'],
                'unit' => $validated['unit'],
                'target_price_per_unit' => $validated['target_price_per_unit'] ?? null,
                'delivery_district' => $validated['delivery_district'],
                'delivery_address' => $validated['delivery_address'] ?? null,
                'target_delivery_date' => $validated['target_delivery_date'],
                'specifications' => $validated['specifications'] ?? null,
                'status' => Rfq::STATUS_OPEN,
                'notes' => $validated['notes'] ?? null,
            ]);

            // If created from bulk requirement, mark requirement as rfq_created
            if (! empty($validated['procurement_requirement_id'])) {
                ProcurementRequirement::where('id', $validated['procurement_requirement_id'])
                    ->update(['status' => ProcurementRequirement::STATUS_RFQ_CREATED]);
            }

            // Process invited sellers
            if (! empty($validated['invited_sellers'])) {
                foreach ($validated['invited_sellers'] as $target) {
                    $parts = explode(':', $target);
                    if (count($parts) === 2) {
                        [$type, $id] = $parts;
                        $inv = [
                            'rfq_id' => $rfq->id,
                            'recipient_type' => $type,
                            'status' => RfqInvitation::STATUS_INVITED,
                        ];

                        $recipientUser = null;

                        if ($type === 'farmer') {
                            $inv['recipient_user_id'] = (int) $id;
                            $recipientUser = User::find($id);
                        } elseif ($type === 'supplier') {
                            $inv['supplier_organization_id'] = (int) $id;
                            $org = SupplierOrganization::find($id);
                            $recipientUser = $org?->users()->first();
                        } elseif ($type === 'fpo') {
                            $inv['fpo_organization_id'] = (int) $id;
                            $fpo = FpoOrganization::find($id);
                            $recipientUser = $fpo?->authorizedUsers()->first();
                        }

                        RfqInvitation::create($inv);

                        // Dispatch database notification
                        if ($recipientUser) {
                            $recipientUser->notify(new RfqCreatedNotification($rfq, $user));
                        }
                    }
                }
            }

            return $rfq;
        });

        return redirect()->route('rfqs.show', $rfq->id)
            ->with('success', "RFQ #{$rfq->rfq_number} successfully published and dispatched to invited sellers.");
    }

    /**
     * View RFQ details.
     */
    public function show(Rfq $rfq)
    {
        $user = $this->getActiveUser();
        $this->authorize('view', $rfq);

        $rfq->load([
            'buyer',
            'requirement',
            'produceLot.crop',
            'product',
            'collectiveListing',
            'invitations.supplierOrganization',
            'invitations.fpoOrganization',
            'invitations.recipientUser',
            'quotations.sellerUser',
            'quotations.supplierOrganization',
            'quotations.fpoOrganization',
        ]);

        $existingQuotation = null;
        if (! $user->isBuyer() && ! $user->isAdmin()) {
            $existingQuotation = $rfq->quotations()->where('seller_user_id', $user->id)->first();
        }

        return view('pages.rfq-detail', compact('rfq', 'user', 'existingQuotation'));
    }

    /**
     * Buyer Quotation Comparison Matrix.
     * STRICT CONFIDENTIALITY: Only owning buyer or admin can compare!
     */
    public function compare(Rfq $rfq)
    {
        $user = $this->getActiveUser();
        $this->authorize('compare', $rfq);

        $rfq->load([
            'buyer',
            'quotations.sellerUser',
            'quotations.supplierOrganization',
            'quotations.fpoOrganization',
        ]);

        $quotations = $rfq->quotations()->orderBy('total_amount', 'asc')->get();

        return view('pages.rfq-compare', compact('rfq', 'quotations', 'user'));
    }

    /**
     * Close or cancel an RFQ.
     */
    public function close(Rfq $rfq)
    {
        $user = $this->getActiveUser();
        $this->authorize('update', $rfq);

        $rfq->update(['status' => Rfq::STATUS_CLOSED]);

        return redirect()->route('rfqs.show', $rfq->id)
            ->with('success', "RFQ #{$rfq->rfq_number} has been closed.");
    }
}
