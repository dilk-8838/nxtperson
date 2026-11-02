<?php

namespace App\Http\Controllers;

use App\Models\Crop;
use App\Models\FpoCollectiveListing;
use App\Models\FpoGateEntry;
use App\Models\FpoGateEntryItem;
use App\Models\FpoMember;
use App\Models\FpoOperationAuditLog;
use App\Models\FpoOperationalException;
use App\Models\FpoOrganization;
use App\Models\ProduceLot;
use App\Models\PurchaseOrder;
use App\Models\User;
use App\Notifications\GateEntryRegisteredNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class FpoGateEntryController extends Controller
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
                    'description' => 'FPO aggregating smallholders across Rajkot and Gondal.',
                    'registered_address' => 'Station Road, APMC Compound, Gondal',
                    'village' => 'Gondal',
                    'taluka' => 'Gondal',
                    'district' => 'Rajkot',
                    'state' => 'Gujarat',
                    'pincode' => '360311',
                    'authorized_representative_name' => $user->name,
                    'contact_email' => $user->email,
                    'contact_phone' => $user->phone ?? '9876543250',
                    'bank_name' => 'State Bank of India',
                    'bank_account_number' => '38992010992',
                    'ifsc_code' => 'SBIN0000382',
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
     * Gate operations register and dashboard.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('login');
        }

        $this->authorize('viewAny', FpoGateEntry::class);

        [$currentUser, $organization] = $this->getFpoContext();

        $query = FpoGateEntry::with(['fpoOrganization', 'farmer', 'sourceProduceLot', 'acceptedWeightment', 'latestApproval'])
            ->orderBy('id', 'desc');

        if (! $user->isAdmin() && $organization) {
            $query->where('fpo_organization_id', $organization->id);
        } elseif ($request->filled('fpo_id')) {
            $query->where('fpo_organization_id', $request->input('fpo_id'));
        }

        // Search & Filters
        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('entry_number', 'like', "%{$search}%")
                    ->orWhere('vehicle_number', 'like', "%{$search}%")
                    ->orWhere('delivering_party_name', 'like', "%{$search}%")
                    ->orWhere('crop_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('crop')) {
            $query->where('crop_name', $request->input('crop'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('entry_date_time', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('entry_date_time', '<=', $request->input('date_to'));
        }

        if ($request->filled('delivering_party_type')) {
            $query->where('delivering_party_type', $request->input('delivering_party_type'));
        }

        $entries = $query->paginate(12)->withQueryString();

        // Operational counts computed strictly from persisted records
        $scopedQuery = FpoGateEntry::query();
        if (! $user->isAdmin() && $organization) {
            $scopedQuery->where('fpo_organization_id', $organization->id);
        }

        $today = now()->toDateString();
        $stats = [
            'total_entries' => (clone $scopedQuery)->count(),
            'entries_today' => (clone $scopedQuery)->whereDate('entry_date_time', $today)->count(),
            'vehicles_inside' => (clone $scopedQuery)->where('entry_type', FpoGateEntry::ENTRY_TYPE_VEHICLE)
                ->whereNotIn('status', [FpoGateEntry::STATUS_EXITED, FpoGateEntry::STATUS_CANCELLED])
                ->count(),
            'awaiting_weightment' => (clone $scopedQuery)->whereIn('status', [FpoGateEntry::STATUS_REGISTERED, FpoGateEntry::STATUS_ENTERED])->count(),
            'awaiting_testing' => (clone $scopedQuery)->whereIn('status', [FpoGateEntry::STATUS_WEIGHED, FpoGateEntry::STATUS_SAMPLING_PENDING])->count(),
            'awaiting_approval' => (clone $scopedQuery)->where('status', FpoGateEntry::STATUS_QUALITY_PENDING_APPROVAL)->count(),
            'accepted_deliveries' => (clone $scopedQuery)->whereIn('status', [FpoGateEntry::STATUS_APPROVED, FpoGateEntry::STATUS_PARTIALLY_ACCEPTED, FpoGateEntry::STATUS_INVENTORY_POSTED])->count(),
            'rejected_deliveries' => (clone $scopedQuery)->where('status', FpoGateEntry::STATUS_REJECTED)->count(),
            'awaiting_exit' => (clone $scopedQuery)->where('entry_type', FpoGateEntry::ENTRY_TYPE_VEHICLE)
                ->whereIn('status', [FpoGateEntry::STATUS_APPROVED, FpoGateEntry::STATUS_PARTIALLY_ACCEPTED, FpoGateEntry::STATUS_INVENTORY_POSTED, FpoGateEntry::STATUS_REJECTED, FpoGateEntry::STATUS_EXIT_AUTHORIZED])
                ->where('status', '!=', FpoGateEntry::STATUS_EXITED)
                ->count(),
            'open_exceptions' => FpoOperationalException::when(! $user->isAdmin() && $organization, fn ($q) => $q->where('fpo_organization_id', $organization->id))
                ->whereIn('status', ['open', 'under_review', 'reopened'])
                ->count(),
        ];

        $crops = Crop::where('is_active', true)->get();
        $organizations = $user->isAdmin() ? FpoOrganization::all() : collect([$organization]);

        return view('pages.fpo-gate-dashboard', compact('entries', 'stats', 'crops', 'organizations', 'organization', 'user'));
    }

    /**
     * Show form to register an incoming delivery.
     */
    public function create()
    {
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('login');
        }

        $this->authorize('create', FpoGateEntry::class);

        [$currentUser, $organization] = $this->getFpoContext();

        // Active member farmers of this FPO
        $memberFarmers = $organization->memberFarmers()
            ->wherePivot('status', 'active')
            ->get();

        // Eligible source produce lots belonging to member farmers
        $memberFarmerIds = $memberFarmers->pluck('id')->toArray();
        $sourceLots = ProduceLot::whereIn('farmer_id', $memberFarmerIds)
            ->whereNotIn('status', [ProduceLot::STATUS_DRAFT, ProduceLot::STATUS_CANCELLED])
            ->orderBy('id', 'desc')
            ->get();

        // Collective listings of this FPO
        $collectiveListings = $organization->collectiveListings()
            ->whereIn('status', [FpoCollectiveListing::STATUS_PUBLISHED, FpoCollectiveListing::STATUS_PARTIALLY_SOLD])
            ->get();

        // Pending Purchase Orders
        $purchaseOrders = PurchaseOrder::where('fpo_organization_id', $organization->id)
            ->whereIn('status', [PurchaseOrder::STATUS_CONFIRMED, PurchaseOrder::STATUS_PROCESSING, PurchaseOrder::STATUS_READY_FOR_DISPATCH])
            ->get();

        $crops = Crop::where('is_active', true)->get();

        return view('pages.fpo-gate-entry-create', compact('organization', 'memberFarmers', 'sourceLots', 'collectiveListings', 'purchaseOrders', 'crops', 'user'));
    }

    /**
     * Persist incoming delivery gate entry and items.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        if (! $user) {
            abort(401, 'Unauthenticated');
        }

        $this->authorize('create', FpoGateEntry::class);

        [$currentUser, $organization] = $this->getFpoContext();

        $validated = $request->validate([
            'gate_point' => 'required|string|max:50',
            'entry_type' => 'required|string|in:vehicle_delivery,walk_in,transfer,return',
            'vehicle_number' => 'nullable|required_if:entry_type,vehicle_delivery|string|max:30',
            'driver_name' => 'nullable|string|max:100',
            'driver_phone' => 'nullable|string|max:20',
            'delivering_party_type' => 'required|string|in:farmer,supplier,transporter,other',
            'farmer_user_id' => 'nullable|exists:users,id',
            'delivering_party_name' => 'required|string|max:150',
            'delivering_party_contact' => 'nullable|string|max:50',
            'purchase_order_id' => 'nullable|exists:purchase_orders,id',
            'source_produce_lot_id' => 'nullable|exists:produce_lots,id',
            'fpo_collective_listing_id' => 'nullable|exists:fpo_collective_listings,id',
            'crop_name' => 'required|string|max:100',
            'variety' => 'nullable|string|max:100',
            'declared_quantity' => 'required|numeric|min:0.1|max:1000000',
            'declared_unit' => 'required|string|in:kg,quintal,tonne',
            'initial_condition' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:1000',
            'items' => 'nullable|array',
            'items.*.crop_name' => 'required_with:items|string|max:100',
            'items.*.variety' => 'nullable|string|max:100',
            'items.*.declared_quantity' => 'required_with:items|numeric|min:0.1|max:1000000',
            'items.*.declared_unit' => 'required_with:items|string|in:kg,quintal,tonne',
            'items.*.produce_lot_id' => 'nullable|exists:produce_lots,id',
        ]);

        // Integrity checks: Validate source lot belonging & membership
        if (! empty($validated['source_produce_lot_id'])) {
            $lot = ProduceLot::findOrFail($validated['source_produce_lot_id']);

            // Verify member affiliation with this FPO
            $isMember = $organization->memberFarmers()
                ->where('users.id', $lot->farmer_id)
                ->wherePivot('status', 'active')
                ->exists();

            if (! $isMember && ! $user->isAdmin()) {
                throw ValidationException::withMessages([
                    'source_produce_lot_id' => "Produce Lot #{$lot->lot_number} belongs to a farmer who is not an active member of {$organization->name}.",
                ]);
            }
        }

        // Verify FPO collective listing belongs strictly to this FPO
        if (! empty($validated['fpo_collective_listing_id'])) {
            $collective = FpoCollectiveListing::findOrFail($validated['fpo_collective_listing_id']);
            if ($collective->fpo_organization_id !== $organization->id) {
                throw ValidationException::withMessages([
                    'fpo_collective_listing_id' => 'The selected collective listing belongs to an unrelated FPO.',
                ]);
            }
        }

        // Verify purchase order belongs strictly to this FPO
        if (! empty($validated['purchase_order_id'])) {
            $po = PurchaseOrder::findOrFail($validated['purchase_order_id']);
            if ($po->fpo_organization_id && $po->fpo_organization_id !== $organization->id) {
                throw ValidationException::withMessages([
                    'purchase_order_id' => 'The selected purchase order belongs to an unrelated FPO organization.',
                ]);
            }
        }

        $entryNumber = 'GE-'.date('Ymd').'-'.rand(1000, 9999);

        $entry = DB::transaction(function () use ($organization, $validated, $entryNumber, $user) {
            $entry = FpoGateEntry::create([
                'entry_number' => $entryNumber,
                'fpo_organization_id' => $organization->id,
                'entry_date_time' => now(),
                'gate_point' => $validated['gate_point'],
                'entry_type' => $validated['entry_type'],
                'vehicle_number' => $validated['vehicle_number'] ?? null,
                'driver_name' => $validated['driver_name'] ?? null,
                'driver_phone' => $validated['driver_phone'] ?? null,
                'delivering_party_type' => $validated['delivering_party_type'],
                'farmer_user_id' => $validated['farmer_user_id'] ?? null,
                'delivering_party_name' => $validated['delivering_party_name'],
                'delivering_party_contact' => $validated['delivering_party_contact'] ?? null,
                'purchase_order_id' => $validated['purchase_order_id'] ?? null,
                'source_produce_lot_id' => $validated['source_produce_lot_id'] ?? null,
                'fpo_collective_listing_id' => $validated['fpo_collective_listing_id'] ?? null,
                'crop_name' => $validated['crop_name'],
                'variety' => $validated['variety'] ?? null,
                'declared_quantity' => $validated['declared_quantity'],
                'declared_unit' => $validated['declared_unit'],
                'initial_condition' => $validated['initial_condition'] ?? null,
                'status' => FpoGateEntry::STATUS_ENTERED,
                'created_by' => $user->id,
                'notes' => $validated['notes'] ?? null,
            ]);

            // Persist Line Items
            if (! empty($validated['items']) && count($validated['items']) > 0) {
                foreach ($validated['items'] as $index => $itemData) {
                    FpoGateEntryItem::create([
                        'fpo_gate_entry_id' => $entry->id,
                        'item_number' => "{$entry->entry_number}-ITM".($index + 1),
                        'produce_lot_id' => $itemData['produce_lot_id'] ?? null,
                        'crop_name' => $itemData['crop_name'],
                        'variety' => $itemData['variety'] ?? null,
                        'declared_quantity' => $itemData['declared_quantity'],
                        'declared_unit' => $itemData['declared_unit'],
                        'status' => FpoGateEntryItem::STATUS_PENDING,
                    ]);
                }
            } else {
                // Single line item created by default matching the gate entry
                FpoGateEntryItem::create([
                    'fpo_gate_entry_id' => $entry->id,
                    'item_number' => "{$entry->entry_number}-ITM1",
                    'produce_lot_id' => $entry->source_produce_lot_id,
                    'crop_name' => $entry->crop_name,
                    'variety' => $entry->variety,
                    'declared_quantity' => $entry->declared_quantity,
                    'declared_unit' => $entry->declared_unit,
                    'status' => FpoGateEntryItem::STATUS_PENDING,
                ]);
            }

            // Append tamper-evident audit log
            FpoOperationAuditLog::log(
                fpoOrgId: $organization->id,
                gateEntryId: $entry->id,
                entityType: 'gate_entry',
                entityId: $entry->id,
                action: 'created',
                actor: $user,
                fromState: null,
                toState: FpoGateEntry::STATUS_ENTERED,
                reason: 'Gate entry registered at entry point.',
                metadata: [
                    'entry_type' => $entry->entry_type,
                    'vehicle_number' => $entry->vehicle_number,
                    'declared_quantity' => "{$entry->declared_quantity} {$entry->declared_unit}",
                ]
            );

            return $entry;
        });

        // Notifications
        if ($entry->farmer) {
            $entry->farmer->notify(new GateEntryRegisteredNotification($entry));
        }

        return redirect()->route('fpo.gate-entries.show', $entry->id)
            ->with('success', "Gate Entry #{$entry->entry_number} successfully registered. Vehicle is now inside the procurement centre.");
    }

    /**
     * Show detailed gate entry lifecycle view.
     */
    public function show(FpoGateEntry $entry)
    {
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('login');
        }

        $this->authorize('view', $entry);

        $entry->load([
            'fpoOrganization',
            'farmer',
            'creator',
            'purchaseOrder',
            'sourceProduceLot',
            'collectiveListing',
            'items.produceLot',
            'items.weightments',
            'items.samples.qualityTests',
            'items.approvals',
            'weightments.originalWeightment',
            'weightments.reweighments',
            'weightments.resolver',
            'samples.qualityTests.tester',
            'approvals.approver',
            'inventoryPostings.produceLot',
            'inventoryPostings.collectiveListing',
            'gateExits.authorizer',
            'exceptions.creator',
            'exceptions.resolver',
            'auditLogs.actor',
        ]);

        return view('pages.fpo-gate-entry-detail', compact('entry', 'user'));
    }

    /**
     * Update operational status with state machine validation and audit log.
     */
    public function updateStatus(Request $request, FpoGateEntry $entry)
    {
        $user = Auth::user();
        if (! $user) {
            abort(401, 'Unauthenticated');
        }

        $this->authorize('update', $entry);

        $validated = $request->validate([
            'status' => 'required|string|in:entered,weighed,sampling_pending,testing_in_progress,quality_pending_approval,on_hold,quarantined,cancelled',
            'reason' => 'nullable|string|max:500',
        ]);

        $previousStatus = $entry->status;
        $newStatus = $validated['status'];

        DB::transaction(function () use ($entry, $previousStatus, $newStatus, $validated, $user) {
            $entry->status = $newStatus;
            $entry->save();

            FpoOperationAuditLog::log(
                fpoOrgId: $entry->fpo_organization_id,
                gateEntryId: $entry->id,
                entityType: 'gate_entry',
                entityId: $entry->id,
                action: 'status_changed',
                actor: $user,
                fromState: $previousStatus,
                toState: $newStatus,
                reason: $validated['reason'] ?? "Operational state updated to {$entry->getStatusLabel()}."
            );
        });

        return redirect()->route('fpo.gate-entries.show', $entry->id)
            ->with('success', "Operational status updated to {$entry->getStatusLabel()}.");
    }
}
