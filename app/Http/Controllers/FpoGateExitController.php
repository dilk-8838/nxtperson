<?php

namespace App\Http\Controllers;

use App\Models\FpoGateEntry;
use App\Models\FpoGateExit;
use App\Models\FpoMember;
use App\Models\FpoOperationAuditLog;
use App\Models\FpoOperationalException;
use App\Models\FpoOrganization;
use App\Models\PurchaseOrder;
use App\Notifications\GateExitAuthorizedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class FpoGateExitController extends Controller
{
    /**
     * Resolve active FPO user and organization context.
     */
    protected function getFpoContext(): array
    {
        $user = Auth::user();
        if (! $user) {
            abort(401, 'Unauthenticated');
        }

        if ($user->isAdmin()) {
            $org = FpoOrganization::first();

            return [$user, $org];
        }

        if (! $user->isFpo()) {
            abort(403, 'Unauthorized access to FPO Gate Operations.');
        }

        $org = $user->primaryFpoOrganization();
        if (! $org) {
            $org = FpoOrganization::firstOrCreate(
                ['slug' => 'saurashtra-kisan-producer-co'],
                [
                    'name' => 'Saurashtra Kisan Producer Company Ltd',
                    'organization_type' => 'Farmer Producer Company (FPC)',
                    'registration_number' => 'U01100GJ2023PTC140221',
                    'incorporation_year' => 2023,
                    'district' => 'Rajkot',
                    'state' => 'Gujarat',
                    'pincode' => '360311',
                    'authorized_representative_name' => $user->name,
                    'contact_email' => $user->email,
                    'contact_phone' => '9876543250',
                    'verification_status' => FpoOrganization::STATUS_APPROVED,
                    'verified_at' => now(),
                ]
            );

            FpoMember::firstOrCreate(
                ['fpo_organization_id' => $org->id, 'user_id' => $user->id],
                ['role_in_fpo' => 'admin', 'is_primary' => true]
            );
        }

        return [$user, $org];
    }

    /**
     * Display gate exits register.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('login');
        }

        [$currentUser, $organization] = $this->getFpoContext();

        $query = FpoGateExit::with(['fpoOrganization', 'gateEntry', 'purchaseOrder.buyer', 'authorizer', 'recorder'])
            ->orderBy('id', 'desc');

        if (! $user->isAdmin() && $organization) {
            $query->where('fpo_organization_id', $organization->id);
        }

        if ($request->filled('movement_type')) {
            $query->where('movement_type', $request->input('movement_type'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('exit_number', 'like', "%{$search}%")
                    ->orWhere('vehicle_number', 'like', "%{$search}%")
                    ->orWhere('driver_name', 'like', "%{$search}%");
            });
        }

        $exits = $query->paginate(12)->withQueryString();

        $stats = [
            'total_exits' => (clone $query)->count(),
            'authorized' => (clone $query)->where('status', FpoGateExit::STATUS_AUTHORIZED)->count(),
            'exited' => (clone $query)->where('status', FpoGateExit::STATUS_EXITED)->count(),
            'dispatched_volume' => (clone $query)->where('status', FpoGateExit::STATUS_EXITED)->sum('dispatched_quantity'),
        ];

        return view('pages.fpo-gate-exit-index', compact('exits', 'stats', 'organization', 'user'));
    }

    /**
     * Show form to authorize gate exit.
     */
    public function create(Request $request)
    {
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('login');
        }

        [$currentUser, $organization] = $this->getFpoContext();

        $gateEntryId = $request->input('gate_entry_id');
        $gateEntry = $gateEntryId ? FpoGateEntry::find($gateEntryId) : null;

        // Pending Purchase Orders eligible for dispatch
        $eligibleOrders = PurchaseOrder::where('fpo_organization_id', $organization->id)
            ->whereIn('status', [PurchaseOrder::STATUS_PROCESSING, PurchaseOrder::STATUS_READY_FOR_DISPATCH, PurchaseOrder::STATUS_CONFIRMED])
            ->get();

        // Vehicles inside premises
        $vehiclesInside = FpoGateEntry::where('fpo_organization_id', $organization->id)
            ->where('entry_type', FpoGateEntry::ENTRY_TYPE_VEHICLE)
            ->whereNotIn('status', [FpoGateEntry::STATUS_EXITED, FpoGateEntry::STATUS_CANCELLED])
            ->get();

        return view('pages.fpo-gate-exit-create', compact('gateEntry', 'eligibleOrders', 'vehiclesInside', 'organization', 'user'));
    }

    /**
     * Store authorized gate exit record.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        if (! $user) {
            abort(401, 'Unauthenticated');
        }

        [$currentUser, $organization] = $this->getFpoContext();

        $validated = $request->validate([
            'movement_type' => 'required|string|in:order_dispatch,transfer,return_rejected,empty_exit',
            'fpo_gate_entry_id' => 'nullable|exists:fpo_gate_entries,id',
            'purchase_order_id' => 'nullable|exists:purchase_orders,id',
            'vehicle_number' => 'nullable|string|max:30',
            'driver_name' => 'nullable|string|max:100',
            'driver_phone' => 'nullable|string|max:20',
            'destination' => 'nullable|string|max:200',
            'dispatched_quantity' => 'required|numeric|min:0|max:1000000',
            'unit' => 'required|string|in:kg,quintal,tonne',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Purchase Order Dispatch Checks
        if ($validated['movement_type'] === FpoGateExit::MOVEMENT_ORDER_DISPATCH) {
            if (empty($validated['purchase_order_id'])) {
                throw ValidationException::withMessages([
                    'purchase_order_id' => 'A valid Purchase Order reference is required for commercial order dispatch.',
                ]);
            }

            $order = PurchaseOrder::findOrFail($validated['purchase_order_id']);

            // Disallow dispatch of cancelled order
            if ($order->status === PurchaseOrder::STATUS_CANCELLED) {
                throw ValidationException::withMessages([
                    'purchase_order_id' => 'Cannot dispatch a cancelled purchase order.',
                ]);
            }

            // Check if quantity does not exceed outstanding quantity
            $alreadyDispatched = FpoGateExit::where('purchase_order_id', $order->id)
                ->whereIn('status', [FpoGateExit::STATUS_AUTHORIZED, FpoGateExit::STATUS_EXITED])
                ->sum('dispatched_quantity');

            $remainingAuthorized = max(0, (float) $order->quantity - $alreadyDispatched);

            if ((float) $validated['dispatched_quantity'] > ($remainingAuthorized + 0.01)) {
                throw ValidationException::withMessages([
                    'dispatched_quantity' => "Dispatched quantity ({$validated['dispatched_quantity']} {$validated['unit']}) exceeds remaining authorized order volume ({$remainingAuthorized} {$order->unit}).",
                ]);
            }
        }

        // Gate Entry checks if attached
        if (! empty($validated['fpo_gate_entry_id'])) {
            $gateEntry = FpoGateEntry::findOrFail($validated['fpo_gate_entry_id']);

            // If entry is vehicle delivery, cannot exit if already exited
            if ($gateEntry->status === FpoGateEntry::STATUS_EXITED) {
                throw ValidationException::withMessages([
                    'fpo_gate_entry_id' => "Gate Entry #{$gateEntry->entry_number} vehicle has already exited premises.",
                ]);
            }
        }

        $exitNumber = 'EXIT-'.date('Ymd').'-'.rand(1000, 9999);

        $exit = DB::transaction(function () use ($organization, $validated, $exitNumber, $user) {
            $exit = FpoGateExit::create([
                'exit_number' => $exitNumber,
                'fpo_organization_id' => $organization->id,
                'fpo_gate_entry_id' => $validated['fpo_gate_entry_id'] ?? null,
                'purchase_order_id' => $validated['purchase_order_id'] ?? null,
                'movement_type' => $validated['movement_type'],
                'vehicle_number' => $validated['vehicle_number'] ?? null,
                'driver_name' => $validated['driver_name'] ?? null,
                'driver_phone' => $validated['driver_phone'] ?? null,
                'destination' => $validated['destination'] ?? null,
                'dispatched_quantity' => $validated['dispatched_quantity'],
                'unit' => $validated['unit'],
                'exit_date_time' => now(),
                'authorized_by' => $user->id,
                'authorizer_name' => $user->name,
                'recorded_by' => $user->id,
                'status' => FpoGateExit::STATUS_AUTHORIZED,
                'notes' => $validated['notes'] ?? null,
            ]);

            // If attached to a gate entry, update status to exit_authorized
            if ($exit->fpo_gate_entry_id) {
                $entry = FpoGateEntry::find($exit->fpo_gate_entry_id);
                if ($entry) {
                    $entry->update(['status' => FpoGateEntry::STATUS_EXIT_AUTHORIZED]);
                }
            }

            // If attached to purchase order, transition order to ready_for_dispatch or dispatched
            if ($exit->purchase_order_id) {
                $order = PurchaseOrder::find($exit->purchase_order_id);
                if ($order && in_array($order->status, [PurchaseOrder::STATUS_PROCESSING, PurchaseOrder::STATUS_READY_FOR_DISPATCH])) {
                    $order->update(['status' => PurchaseOrder::STATUS_READY_FOR_DISPATCH]);
                }
            }

            FpoOperationAuditLog::log(
                fpoOrgId: $organization->id,
                gateEntryId: $exit->fpo_gate_entry_id,
                entityType: 'gate_exit',
                entityId: $exit->id,
                action: 'exit_authorized',
                actor: $user,
                fromState: null,
                toState: FpoGateExit::STATUS_AUTHORIZED,
                reason: "Gate exit authorized: Movement type '{$exit->movement_type}', Quantity: {$exit->dispatched_quantity} {$exit->unit}."
            );

            return $exit;
        });

        return redirect()->route('fpo.gate-exits.index')
            ->with('success', "Gate Exit #{$exit->exit_number} successfully authorized.");
    }

    /**
     * Confirm actual vehicle exit from premises.
     */
    public function confirmActualExit(Request $request, FpoGateExit $exit)
    {
        $user = Auth::user();
        if (! $user) {
            abort(401, 'Unauthenticated');
        }

        $this->authorize('confirmHandover', $exit);

        if ($exit->status === FpoGateExit::STATUS_EXITED) {
            return redirect()->route('fpo.gate-exits.index')
                ->with('info', "Exit #{$exit->exit_number} was already confirmed as exited at {$exit->actual_exit_at}.");
        }

        DB::transaction(function () use ($exit, $user) {
            $exit->update([
                'status' => FpoGateExit::STATUS_EXITED,
                'actual_exit_at' => now(),
            ]);

            // Update attached gate entry status
            if ($exit->fpo_gate_entry_id) {
                $entry = FpoGateEntry::find($exit->fpo_gate_entry_id);
                if ($entry) {
                    $entry->update(['status' => FpoGateEntry::STATUS_EXITED]);
                }
            }

            // Update attached purchase order status to dispatched (In Transit)
            if ($exit->purchase_order_id) {
                $order = PurchaseOrder::find($exit->purchase_order_id);
                if ($order && in_array($order->status, [PurchaseOrder::STATUS_READY_FOR_DISPATCH, PurchaseOrder::STATUS_PROCESSING])) {
                    $order->update(['status' => PurchaseOrder::STATUS_DISPATCHED]);
                }
            }

            FpoOperationAuditLog::log(
                fpoOrgId: $exit->fpo_organization_id,
                gateEntryId: $exit->fpo_gate_entry_id,
                entityType: 'gate_exit',
                entityId: $exit->id,
                action: 'exited',
                actor: $user,
                fromState: FpoGateExit::STATUS_AUTHORIZED,
                toState: FpoGateExit::STATUS_EXITED,
                reason: "Vehicle {$exit->vehicle_number} has physically exited premises at ".now()->toTimeString()
            );
        });

        return redirect()->route('fpo.gate-exits.index')
            ->with('success', "Vehicle physical exit confirmed for Exit #{$exit->exit_number}.");
    }

    /**
     * Handover / buyer delivery confirmation.
     */
    public function confirmHandover(Request $request, FpoGateExit $exit)
    {
        $user = Auth::user();
        if (! $user) {
            abort(401, 'Unauthenticated');
        }

        $this->authorize('confirmHandover', $exit);

        $validated = $request->validate([
            'handover_notes' => 'nullable|string|max:500',
        ]);

        DB::transaction(function () use ($exit, $validated, $user) {
            $exit->update([
                'handover_confirmed_at' => now(),
                'handover_confirmed_by' => $user->id,
                'handover_notes' => $validated['handover_notes'] ?? "Handover confirmed by {$user->name}.",
            ]);

            // Note: Does NOT automatically mark order as delivered unless explicitly verified
            if ($exit->purchase_order_id) {
                $order = PurchaseOrder::find($exit->purchase_order_id);
                if ($order && $order->status === PurchaseOrder::STATUS_DISPATCHED) {
                    $order->update([
                        'status' => PurchaseOrder::STATUS_DELIVERED,
                        'actual_delivery_date' => now()->toDateString(),
                    ]);
                }
            }

            FpoOperationAuditLog::log(
                fpoOrgId: $exit->fpo_organization_id,
                gateEntryId: $exit->fpo_gate_entry_id,
                entityType: 'gate_exit',
                entityId: $exit->id,
                action: 'handover_confirmed',
                actor: $user,
                reason: "Handover delivery confirmation recorded by {$user->name}."
            );
        });

        return redirect()->route('fpo.gate-exits.index')
            ->with('success', "Handover / Delivery confirmation successfully recorded for Exit #{$exit->exit_number}.");
    }
}
