<?php

namespace App\Http\Controllers;

use App\Models\Crop;
use App\Models\FpoCollectiveListing;
use App\Models\FpoMember;
use App\Models\FpoMemberAggregation;
use App\Models\FpoOrganization;
use App\Models\ProduceLot;
use App\Models\User;
use App\Notifications\FpoProduceAggregatedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class FpoAggregationController extends Controller
{
    /**
     * Resolve active FPO user and their organization.
     */
    protected function getFpoContext(): array
    {
        $user = Auth::user();
        if (! $user || ! $user->isFpo()) {
            $user = User::firstOrCreate(
                ['email' => 'fpo.saurashtra@demo.nxtperson.farm'],
                [
                    'name' => 'Saurashtra Kisan Producer Co. (Demo FPO)',
                    'phone' => '9876543250',
                    'role' => 'fpo',
                    'status' => 'active',
                    'district' => 'Rajkot',
                    'state' => 'Gujarat',
                    'pincode' => '360001',
                    'password' => bcrypt('demo1234'),
                ]
            );
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
                    'description' => 'Farmer producer organization aggregating 450+ smallholder cotton, groundnut, and cumin growers across Rajkot and Gondal.',
                    'registered_address' => 'Station Road, APMC Compound, Gondal',
                    'village' => 'Gondal',
                    'taluka' => 'Gondal',
                    'district' => $user->district ?? 'Rajkot',
                    'state' => 'Gujarat',
                    'pincode' => '360311',
                    'authorized_representative_name' => $user->name,
                    'contact_email' => $user->email,
                    'contact_phone' => $user->phone ?? '9876543250',
                    'bank_name' => 'State Bank of India (Gondal Branch)',
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
     * Display FPO collective listings and member aggregations.
     */
    public function index(Request $request)
    {
        [$user, $organization] = $this->getFpoContext();

        $query = FpoCollectiveListing::with(['crop', 'memberAggregations.farmer'])
            ->where('fpo_organization_id', $organization->id)
            ->orderBy('id', 'desc');

        if ($request->filled('crop')) {
            $query->where('crop_slug', $request->input('crop'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $listings = $query->paginate(10)->withQueryString();
        $crops = Crop::where('is_active', true)->get();

        $stats = [
            'total_listings' => FpoCollectiveListing::where('fpo_organization_id', $organization->id)->count(),
            'total_volume_kg' => FpoCollectiveListing::where('fpo_organization_id', $organization->id)->sum('total_aggregated_quantity_kg'),
            'available_volume_kg' => FpoCollectiveListing::where('fpo_organization_id', $organization->id)->sum('available_quantity_kg'),
            'total_aggregations' => FpoMemberAggregation::where('fpo_organization_id', $organization->id)->count(),
        ];

        return view('pages.fpo-collective-index', compact('listings', 'crops', 'stats', 'organization', 'user'));
    }

    /**
     * Show form to aggregate member produce into a collective listing.
     */
    public function create()
    {
        [$user, $organization] = $this->getFpoContext();

        // Fetch active member farmers belonging strictly to this FPO
        $activeMemberUserIds = $organization->farmerMemberships()
            ->where('status', 'active')
            ->pluck('farmer_user_id');

        // Fetch eligible produce lots submitted by active member farmers with available stock
        $eligibleLots = ProduceLot::with(['crop', 'farmer'])
            ->whereIn('farmer_id', $activeMemberUserIds)
            ->whereIn('status', [
                ProduceLot::STATUS_ASSESSED,
                ProduceLot::STATUS_APPROVED_FOR_PROCUREMENT,
                ProduceLot::STATUS_INVENTORY_AVAILABLE,
                ProduceLot::STATUS_PARTIALLY_SOLD,
            ])
            ->where('quantity_in_kg', '>', 0)
            ->orderBy('crop_slug', 'asc')
            ->get();

        $crops = Crop::where('is_active', true)->orderBy('en_name')->get();
        $districts = ['Rajkot', 'Anand', 'Banaskantha', 'Surat', 'Mehsana', 'Junagadh', 'Ahmedabad', 'Kheda', 'Vadodara', 'Amreli', 'Bhavnagar', 'Patan'];

        return view('pages.fpo-collective-create', compact('eligibleLots', 'crops', 'districts', 'organization', 'user'));
    }

    /**
     * Persist collective listing and create traceable member aggregations.
     */
    public function store(Request $request)
    {
        [$user, $organization] = $this->getFpoContext();

        $validated = $request->validate([
            'crop_slug' => 'required|string|exists:crops,slug',
            'variety' => 'required|string|max:100',
            'title' => 'required|string|max:200',
            'asking_price_per_kg' => 'nullable|numeric|min:0',
            'approved_grade' => 'required|string|max:40',
            'district' => 'required|string|max:100',
            'taluka' => 'nullable|string|max:100',
            'pincode' => 'required|digits:6',
            'harvest_period' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:2000',
            'contributions' => 'required|array|min:1',
            'contributions.*.produce_lot_id' => 'required|exists:produce_lots,id',
            'contributions.*.quantity_kg' => 'required|numeric|min:1|max:1000000',
        ], [
            'contributions.required' => 'You must select at least one member produce lot to aggregate.',
        ]);

        $listingNumber = 'FPO-LOT-'.strtoupper(substr($validated['crop_slug'], 0, 3)).'-'.date('Ymd').'-'.rand(1000, 9999);

        // Active members check
        $activeMemberUserIds = $organization->farmerMemberships()
            ->where('status', 'active')
            ->pluck('farmer_user_id')
            ->toArray();

        $collectiveListing = DB::transaction(function () use (
            $organization,
            $validated,
            $listingNumber,
            $activeMemberUserIds
        ) {
            $totalPooledKg = 0;
            $aggregationRecords = [];

            // 1. Process each member contribution with row locking and stock verification
            foreach ($validated['contributions'] as $contrib) {
                $produceLotId = (int) $contrib['produce_lot_id'];
                $commitKg = (float) $contrib['quantity_kg'];

                $lockedLot = ProduceLot::where('id', $produceLotId)->lockForUpdate()->firstOrFail();

                // Validate membership
                if (! in_array($lockedLot->farmer_id, $activeMemberUserIds)) {
                    throw ValidationException::withMessages([
                        'contributions' => "Farmer for Lot #{$lockedLot->lot_number} is not an active member of {$organization->name}.",
                    ]);
                }

                // Check available quantity
                $effectiveAvailableKg = ($lockedLot->available_quantity_kg > 0)
                    ? $lockedLot->available_quantity_kg
                    : ($lockedLot->quantity_in_kg - ($lockedLot->reserved_quantity_kg + $lockedLot->sold_quantity_kg + $lockedLot->aggregated_quantity_kg));

                if ($effectiveAvailableKg < $commitKg) {
                    throw ValidationException::withMessages([
                        'contributions' => "Lot #{$lockedLot->lot_number} only has ".number_format($effectiveAvailableKg, 1).' kg uncommitted stock. Cannot aggregate '.number_format($commitKg, 1).' kg.',
                    ]);
                }

                // Deduct from available, add to aggregated & reserved
                if ($lockedLot->available_quantity_kg > 0) {
                    $lockedLot->available_quantity_kg = max(0, $lockedLot->available_quantity_kg - $commitKg);
                    if ($lockedLot->available_quantity_kg <= 0.001) {
                        $lockedLot->available_quantity_kg = 0;
                    }
                }

                $lockedLot->aggregated_quantity_kg += $commitKg;
                $lockedLot->reserved_quantity_kg += $commitKg;
                $lockedLot->save();

                $totalPooledKg += $commitKg;
                $aggregationRecords[] = [
                    'farmer_user_id' => $lockedLot->farmer_id,
                    'produce_lot_id' => $lockedLot->id,
                    'committed_quantity_kg' => $commitKg,
                    'lot' => $lockedLot,
                ];
            }

            // 2. Create FPO Collective Listing
            $listing = FpoCollectiveListing::create([
                'listing_number' => $listingNumber,
                'fpo_organization_id' => $organization->id,
                'crop_slug' => $validated['crop_slug'],
                'variety' => $validated['variety'],
                'title' => $validated['title'],
                'total_aggregated_quantity_kg' => $totalPooledKg,
                'available_quantity_kg' => $totalPooledKg,
                'reserved_quantity_kg' => 0,
                'sold_quantity_kg' => 0,
                'asking_price_per_kg' => $validated['asking_price_per_kg'] ?? null,
                'approved_grade' => $validated['approved_grade'],
                'district' => $validated['district'],
                'taluka' => $validated['taluka'] ?? null,
                'pincode' => $validated['pincode'],
                'harvest_period' => $validated['harvest_period'] ?? null,
                'description' => $validated['description'] ?? null,
                'status' => FpoCollectiveListing::STATUS_PUBLISHED,
                'published_at' => now(),
            ]);

            // 3. Create Traceable Aggregation Linkages
            foreach ($aggregationRecords as $record) {
                $agg = FpoMemberAggregation::create([
                    'fpo_organization_id' => $organization->id,
                    'fpo_collective_listing_id' => $listing->id,
                    'farmer_user_id' => $record['farmer_user_id'],
                    'produce_lot_id' => $record['produce_lot_id'],
                    'committed_quantity_kg' => $record['committed_quantity_kg'],
                    'status' => FpoMemberAggregation::STATUS_POOLED,
                    'notes' => "Pooled under collective bulk listing #{$listing->listing_number}.",
                ]);

                // Notify contributing farmer
                $farmer = User::find($record['farmer_user_id']);
                if ($farmer) {
                    $farmer->notify(new FpoProduceAggregatedNotification($agg));
                }
            }

            return $listing;
        });

        return redirect()->route('fpo.collective.show', $collectiveListing->id)
            ->with('success', "Collective Produce Listing #{$collectiveListing->listing_number} published successfully with ".number_format((float) $collectiveListing->total_aggregated_quantity_kg, 1).' kg pooled from member farmers.');
    }

    /**
     * Show collective listing details and contributing farmer roster.
     */
    public function show(FpoCollectiveListing $listing)
    {
        [$user, $organization] = $this->getFpoContext();

        if ($listing->fpo_organization_id !== $organization->id && ! $user->isAdmin()) {
            abort(403, 'Unauthorized access to another FPO\'s private collective listing.');
        }

        $listing->load([
            'crop',
            'fpoOrganization',
            'memberAggregations.farmer.farmerProfile',
            'memberAggregations.produceLot',
            'rfqs.quotations',
            'purchaseOrders.buyer',
        ]);

        return view('pages.fpo-collective-detail', compact('listing', 'organization', 'user'));
    }

    /**
     * Withdraw collective listing and safely return uncommitted stock to farmers.
     */
    public function withdraw(FpoCollectiveListing $listing)
    {
        [$user, $organization] = $this->getFpoContext();

        if ($listing->fpo_organization_id !== $organization->id && ! $user->isAdmin()) {
            abort(403, 'Unauthorized.');
        }

        if (in_array($listing->status, [FpoCollectiveListing::STATUS_SOLD_OUT, FpoCollectiveListing::STATUS_WITHDRAWN])) {
            return redirect()->back()->with('error', 'Listing is already inactive.');
        }

        DB::transaction(function () use ($listing) {
            $lockedListing = FpoCollectiveListing::where('id', $listing->id)->lockForUpdate()->firstOrFail();

            // Release member aggregations
            foreach ($lockedListing->memberAggregations as $agg) {
                if ($agg->status === FpoMemberAggregation::STATUS_POOLED) {
                    $lot = ProduceLot::where('id', $agg->produce_lot_id)->lockForUpdate()->first();
                    if ($lot) {
                        $releasedKg = (float) $agg->committed_quantity_kg;
                        $lot->aggregated_quantity_kg = max(0, $lot->aggregated_quantity_kg - $releasedKg);
                        $lot->reserved_quantity_kg = max(0, $lot->reserved_quantity_kg - $releasedKg);
                        $lot->available_quantity_kg += $releasedKg;
                        if (in_array($lot->status, [ProduceLot::STATUS_PARTIALLY_SOLD, ProduceLot::STATUS_SOLD_OUT]) && $lot->available_quantity_kg > 0) {
                            $lot->status = ProduceLot::STATUS_INVENTORY_AVAILABLE;
                        }
                        $lot->save();
                    }

                    $agg->status = FpoMemberAggregation::STATUS_RELEASED;
                    $agg->save();
                }
            }

            $lockedListing->available_quantity_kg = 0;
            $lockedListing->status = FpoCollectiveListing::STATUS_WITHDRAWN;
            $lockedListing->save();
        });

        return redirect()->route('fpo.collective.show', $listing->id)
            ->with('success', "Collective listing #{$listing->listing_number} has been withdrawn and member stock has been released.");
    }
}
