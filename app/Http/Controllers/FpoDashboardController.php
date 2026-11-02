<?php

namespace App\Http\Controllers;

use App\Models\FarmerProfile;
use App\Models\FpoFarmerMembership;
use App\Models\FpoMember;
use App\Models\FpoOrganization;
use App\Models\User;
use App\Notifications\FpoFarmerMembershipNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class FpoDashboardController extends Controller
{
    /**
     * Resolve active FPO user (with demo fallback).
     */
    protected function getFpoUser(): User
    {
        $user = Auth::user();
        if ($user && $user->isFpo()) {
            return $user;
        }

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

        return $user;
    }

    /**
     * Ensure FPO organization exists for user.
     */
    protected function getOrCreateFpoOrg(User $user): FpoOrganization
    {
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

            // Link demo farmer as member
            $demoFarmer = User::where('email', 'suresh.patel@demo.nxtperson.farm')->first();
            if ($demoFarmer) {
                FpoFarmerMembership::firstOrCreate(
                    ['fpo_organization_id' => $org->id, 'farmer_user_id' => $demoFarmer->id],
                    [
                        'member_code' => 'SKPC-MEM-001',
                        'share_units' => 10,
                        'status' => 'active',
                        'joined_at' => now()->subMonths(6)->toDateString(),
                        'notes' => 'Founding member farmer (Gondal cluster).',
                    ]
                );
            }
        }

        return $org;
    }

    /**
     * FPO Cooperative Management Dashboard.
     */
    public function index()
    {
        $user = $this->getFpoUser();
        $organization = $this->getOrCreateFpoOrg($user);

        // Management team
        $teamMembers = $organization->fpoMembers()
            ->with('user')
            ->orderBy('id', 'asc')
            ->get();

        // Farmer memberships
        $farmerMemberships = $organization->farmerMemberships()
            ->with(['farmerUser.farmerProfile', 'farmerUser.produceLots'])
            ->orderBy('id', 'desc')
            ->paginate(10);

        // Aggregated statistics calculated from real records
        $totalMembers = $organization->farmerMemberships()->count();
        $activeMembers = $organization->farmerMemberships()->where('status', 'active')->count();
        $totalAcreage = $organization->totalMemberAcreage();
        $distinctCrops = $organization->distinctMemberCrops();

        $notifications = $user->notifications()->take(6)->get();

        $memberships = $farmerMemberships;
        $activeMemberCount = $activeMembers;
        $memberCrops = $distinctCrops;

        return view('pages.fpo-dashboard', compact(
            'user',
            'organization',
            'teamMembers',
            'farmerMemberships',
            'memberships',
            'totalMembers',
            'activeMembers',
            'activeMemberCount',
            'totalAcreage',
            'distinctCrops',
            'memberCrops',
            'notifications'
        ));
    }

    /**
     * Update FPO organization profile.
     */
    public function updateProfile(Request $request)
    {
        $user = $this->getFpoUser();
        $organization = $this->getOrCreateFpoOrg($user);

        if ($user->id) {
            $this->authorize('update', $organization);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'organization_type' => ['required', 'string', 'max:100'],
            'registration_number' => ['required', 'string', 'max:50'],
            'incorporation_year' => ['nullable', 'integer', 'min:1950', 'max:'.date('Y')],
            'description' => ['nullable', 'string', 'max:2000'],
            'registered_address' => ['required', 'string', 'max:500'],
            'village' => ['nullable', 'string', 'max:100'],
            'taluka' => ['nullable', 'string', 'max:100'],
            'district' => ['required', 'string', 'max:100'],
            'state' => ['required', 'string', 'max:100'],
            'pincode' => ['nullable', 'string', 'max:10'],
            'authorized_representative_name' => ['required', 'string', 'max:255'],
            'contact_email' => ['required', 'email', 'max:255'],
            'contact_phone' => ['required', 'string', 'min:10', 'max:20'],
            'bank_name' => ['nullable', 'string', 'max:100'],
            'bank_account_number' => ['nullable', 'string', 'max:50'],
            'ifsc_code' => ['nullable', 'string', 'max:20'],
        ]);

        $organization->update($validated);

        return redirect()->back()->with('role_message', 'FPO organization profile updated successfully.');
    }

    /**
     * Enroll or link farmer to FPO organization.
     */
    public function addFarmerMember(Request $request)
    {
        $user = $this->getFpoUser();
        $organization = $this->getOrCreateFpoOrg($user);

        if ($user->id) {
            $this->authorize('manageMembers', $organization);
        }

        $validated = $request->validate([
            'farmer_identifier' => ['nullable', 'string', 'max:255'],
            'farmer_name' => ['nullable', 'string', 'max:255'],
            'farmer_phone' => ['nullable', 'string', 'min:10', 'max:20'],
            'farmer_email' => ['nullable', 'email', 'max:255'],
            'member_code' => ['nullable', 'string', 'max:30'],
            'share_units' => ['required', 'integer', 'min:1', 'max:1000'],
            'total_acreage' => ['nullable', 'numeric', 'min:0'],
            'crops' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $farmerUser = null;
        $cleanIdentifierPhone = '';
        if ($request->filled('farmer_identifier')) {
            $identifier = trim($request->input('farmer_identifier'));
            $cleanIdentifierPhone = preg_replace('/[^0-9]/', '', $identifier);
            $farmerUser = User::where('id', $identifier)
                ->orWhere('email', $identifier)
                ->when(strlen($cleanIdentifierPhone) >= 10, fn ($q) => $q->orWhere('phone', $cleanIdentifierPhone))
                ->first();
        }

        if (! $farmerUser && $request->filled('farmer_phone')) {
            $cleanPhone = preg_replace('/[^0-9]/', '', $validated['farmer_phone']);
            $farmerUser = User::where('phone', $cleanPhone)->first();
        }

        // Find or create farmer user if not found
        if (! $farmerUser) {
            $name = $validated['farmer_name'] ?? 'Member Farmer';
            $phone = ! empty($validated['farmer_phone'])
                ? preg_replace('/[^0-9]/', '', $validated['farmer_phone'])
                : ($cleanIdentifierPhone ?: '98765'.rand(10000, 99999));

            $farmerUser = User::create([
                'name' => $name,
                'email' => $validated['farmer_email'] ?? "farmer.{$phone}@fpo.nxtperson.farm",
                'phone' => $phone,
                'role' => 'farmer',
                'status' => 'active',
                'district' => $organization->district,
                'state' => $organization->state,
                'password' => Hash::make(Str::random(16)),
            ]);
        }

        // Ensure farmer has farm profile
        if (! $farmerUser->farmerProfile()->exists()) {
            $cropsList = ! empty($validated['crops'])
                ? array_map('trim', explode(',', $validated['crops']))
                : ['cotton'];

            FarmerProfile::create([
                'user_id' => $farmerUser->id,
                'farm_name' => "{$farmerUser->name}'s Farm",
                'district' => $organization->district,
                'state' => $organization->state,
                'total_acreage' => $validated['total_acreage'] ?? 5.0,
                'cultivated_acreage' => $validated['total_acreage'] ?? 5.0,
                'crops_cultivated' => $cropsList,
                'verification_status' => 'verified',
                'verified_at' => now(),
            ]);
        }

        // Generate member code if not provided
        $code = $validated['member_code'] ?? ('MEM-'.strtoupper(Str::random(6)));

        // Create or update membership linkage
        $membership = FpoFarmerMembership::updateOrCreate(
            ['fpo_organization_id' => $organization->id, 'farmer_user_id' => $farmerUser->id],
            [
                'member_code' => $code,
                'share_units' => $validated['share_units'],
                'status' => 'active',
                'joined_at' => now()->toDateString(),
                'notes' => $validated['notes'] ?? null,
            ]
        );

        // Send notification to farmer
        $farmerUser->notify(new FpoFarmerMembershipNotification($membership, 'registered'));

        return redirect()->back()->with('role_message', "Farmer '{$farmerUser->name}' enrolled in {$organization->name} (Code: {$code}).");
    }

    /**
     * Toggle farmer membership status.
     */
    public function updateMemberStatus(Request $request, FpoFarmerMembership $membership)
    {
        $user = $this->getFpoUser();
        $organization = $this->getOrCreateFpoOrg($user);

        if ($user->id) {
            $this->authorize('manageMembers', $organization);
        }

        if ($membership->fpo_organization_id !== $organization->id) {
            abort(403, 'Unauthorized action on another organization.');
        }

        $validated = $request->validate([
            'status' => ['required', 'string', 'in:active,pending,inactive'],
        ]);

        $membership->update(['status' => $validated['status']]);

        if ($membership->farmerUser) {
            $membership->farmerUser->notify(new FpoFarmerMembershipNotification(
                $membership,
                $validated['status'] === 'active' ? 'updated' : 'deactivated'
            ));
        }

        return redirect()->back()->with('role_message', "Membership status for {$membership->farmerUser->name} updated to {$validated['status']}.");
    }
}
