<?php

namespace App\Http\Controllers;

use App\Models\FpoCollectiveListing;
use App\Models\ProduceLot;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderStatusLog;
use App\Models\User;
use App\Notifications\PurchaseOrderStatusNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PurchaseOrderController extends Controller
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

        return User::where('role', 'buyer')->first() ?? User::first();
    }

    /**
     * Display purchase orders list.
     */
    public function index(Request $request)
    {
        $user = $this->getActiveUser();

        $query = PurchaseOrder::with(['buyer', 'sellerUser', 'supplierOrganization', 'fpoOrganization'])
            ->orderBy('id', 'desc');

        if ($user->isAdmin()) {
            // Admins can see all orders
        } elseif ($user->isBuyer()) {
            $query->where('buyer_id', $user->id);
        } else {
            // Seller view (Farmer / Supplier / FPO)
            $supplierOrgIds = $user->supplierOrganizations()->pluck('supplier_organizations.id')->toArray();
            $fpoOrgIds = $user->fpoOrganizations()->pluck('fpo_organizations.id')->toArray();

            $query->where(function ($q) use ($user, $supplierOrgIds, $fpoOrgIds) {
                $q->where('seller_user_id', $user->id)
                    ->orWhereIn('supplier_organization_id', $supplierOrgIds)
                    ->orWhereIn('fpo_organization_id', $fpoOrgIds);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $orders = $query->paginate(12)->withQueryString();

        $stats = [
            'total' => (clone $query)->count(),
            'active' => (clone $query)->whereIn('status', [PurchaseOrder::STATUS_CONFIRMED, PurchaseOrder::STATUS_PROCESSING, PurchaseOrder::STATUS_READY_FOR_DISPATCH, PurchaseOrder::STATUS_DISPATCHED])->count(),
            'delivered' => (clone $query)->where('status', PurchaseOrder::STATUS_DELIVERED)->count(),
            'total_volume_rupees' => (clone $query)->sum('total_amount'),
        ];

        return view('pages.orders-index', compact('orders', 'stats', 'user'));
    }

    /**
     * View purchase order details.
     */
    public function show(PurchaseOrder $order)
    {
        $user = $this->getActiveUser();
        $this->authorize('view', $order);

        $order->load([
            'buyer',
            'sellerUser',
            'supplierOrganization',
            'fpoOrganization',
            'produceLot.crop',
            'product',
            'collectiveListing',
            'quotation',
            'rfq',
            'statusLogs.actor',
        ]);

        return view('pages.order-detail', compact('order', 'user'));
    }

    /**
     * Update order workflow status according to state machine.
     */
    public function updateStatus(Request $request, PurchaseOrder $order)
    {
        $user = $this->getActiveUser();
        $this->authorize('updateStatus', $order);

        $validated = $request->validate([
            'status' => 'required|string|in:confirmed,processing,ready_for_dispatch,dispatched,delivered',
            'notes' => 'nullable|string|max:500',
            'payment_status' => 'nullable|in:unpaid,payment_pending,escrow_held,paid_offline,refunded',
        ]);

        $newStatus = $validated['status'];
        $previousStatus = $order->status;

        // Strict state machine validation
        $validTransitions = [
            PurchaseOrder::STATUS_PENDING_CONFIRMATION => [PurchaseOrder::STATUS_CONFIRMED],
            PurchaseOrder::STATUS_CONFIRMED => [PurchaseOrder::STATUS_PROCESSING],
            PurchaseOrder::STATUS_PROCESSING => [PurchaseOrder::STATUS_READY_FOR_DISPATCH],
            PurchaseOrder::STATUS_READY_FOR_DISPATCH => [PurchaseOrder::STATUS_DISPATCHED],
            PurchaseOrder::STATUS_DISPATCHED => [PurchaseOrder::STATUS_DELIVERED],
        ];

        if (! $user->isAdmin()) {
            if (! isset($validTransitions[$previousStatus]) || ! in_array($newStatus, $validTransitions[$previousStatus])) {
                throw ValidationException::withMessages([
                    'status' => "Invalid transition from '{$order->getStatusLabel()}' to '{$newStatus}'.",
                ]);
            }
        }

        DB::transaction(function () use ($order, $newStatus, $previousStatus, $validated, $user) {
            $order->status = $newStatus;

            if ($newStatus === PurchaseOrder::STATUS_DELIVERED) {
                $order->actual_delivery_date = now()->toDateString();
            }

            if (! empty($validated['payment_status'])) {
                $order->payment_status = $validated['payment_status'];
            }

            $order->save();

            PurchaseOrderStatusLog::create([
                'purchase_order_id' => $order->id,
                'from_status' => $previousStatus,
                'to_status' => $newStatus,
                'actor_id' => $user->id,
                'notes' => $validated['notes'] ?? "Status updated to {$order->getStatusLabel()}.",
            ]);
        });

        // Notifications
        $recipient = ($user->id === $order->buyer_id) ? $order->sellerUser : $order->buyer;
        if ($recipient) {
            $recipient->notify(new PurchaseOrderStatusNotification($order, $previousStatus));
        }

        return redirect()->route('orders.show', $order->id)
            ->with('success', "Order #{$order->po_number} status updated to {$order->getStatusLabel()}.");
    }

    /**
     * Cancel purchase order and safely release committed stock.
     */
    public function cancel(Request $request, PurchaseOrder $order)
    {
        $user = $this->getActiveUser();
        $this->authorize('cancel', $order);

        $validated = $request->validate([
            'cancellation_reason' => 'required|string|max:500',
        ]);

        $previousStatus = $order->status;
        $orderQty = (float) $order->quantity;

        DB::transaction(function () use ($order, $previousStatus, $orderQty, $validated, $user) {
            // Atomic conditional update to ensure cancellation runs and restores stock exactly once
            $cancelled = PurchaseOrder::where('id', $order->id)
                ->whereIn('status', [PurchaseOrder::STATUS_PENDING_CONFIRMATION, PurchaseOrder::STATUS_CONFIRMED])
                ->update([
                    'status' => PurchaseOrder::STATUS_CANCELLED,
                    'cancellation_reason' => $validated['cancellation_reason'],
                ]);

            if ($cancelled === 0) {
                throw ValidationException::withMessages([
                    'order' => 'This purchase order cannot be cancelled or has already been cancelled.',
                ]);
            }

            // 1. Release committed stock safely back to available pool
            if ($order->produce_lot_id) {
                $lot = ProduceLot::where('id', $order->produce_lot_id)->lockForUpdate()->first();
                if ($lot) {
                    $lot->reserved_quantity_kg = max(0, $lot->reserved_quantity_kg - $orderQty);
                    $lot->available_quantity_kg += $orderQty;
                    $lot->status = ProduceLot::STATUS_INVENTORY_AVAILABLE;
                    $lot->save();
                }
            } elseif ($order->product_id) {
                $product = Product::where('id', $order->product_id)->lockForUpdate()->first();
                if ($product) {
                    $product->stock_quantity += $orderQty;
                    $product->is_available = true;
                    $product->save();
                }
            } elseif ($order->fpo_collective_listing_id) {
                $collective = FpoCollectiveListing::where('id', $order->fpo_collective_listing_id)->lockForUpdate()->first();
                if ($collective) {
                    $collective->reserved_quantity_kg = max(0, $collective->reserved_quantity_kg - $orderQty);
                    $collective->available_quantity_kg += $orderQty;
                    $collective->status = FpoCollectiveListing::STATUS_PUBLISHED;
                    $collective->save();
                }
            }

            // 3. Log status change
            PurchaseOrderStatusLog::create([
                'purchase_order_id' => $order->id,
                'from_status' => $previousStatus,
                'to_status' => PurchaseOrder::STATUS_CANCELLED,
                'actor_id' => $user->id,
                'notes' => "Cancelled by {$user->name}. Reason: {$validated['cancellation_reason']}",
            ]);
        });

        // Notifications
        $recipient = ($user->id === $order->buyer_id) ? $order->sellerUser : $order->buyer;
        if ($recipient) {
            $recipient->notify(new PurchaseOrderStatusNotification($order, $previousStatus));
        }

        return redirect()->route('orders.show', $order->id)
            ->with('success', "Order #{$order->po_number} has been cancelled and stock has been restored.");
    }
}
