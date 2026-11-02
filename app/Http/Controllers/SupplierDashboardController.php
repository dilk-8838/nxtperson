<?php

namespace App\Http\Controllers;

use App\Models\Distributor;
use App\Models\Product;
use App\Models\SupplierMember;
use App\Models\SupplierOrganization;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SupplierDashboardController extends Controller
{
    /**
     * Resolve active supplier user (with demo fallback).
     */
    protected function getSupplierUser(): User
    {
        $user = Auth::user();
        if ($user && $user->isSupplier()) {
            return $user;
        }

        $user = User::firstOrCreate(
            ['email' => 'supplier.agritech@demo.nxtperson.farm'],
            [
                'name' => 'AgriTech Inputs Gujarat (Demo Supplier)',
                'phone' => '9876543220',
                'role' => 'supplier',
                'status' => 'active',
                'district' => 'Anand',
                'state' => 'Gujarat',
                'pincode' => '388001',
                'password' => bcrypt('demo1234'),
            ]
        );

        return $user;
    }

    /**
     * Ensure supplier organization exists for user.
     */
    protected function getOrCreateSupplierOrg(User $user): SupplierOrganization
    {
        $org = $user->primarySupplierOrganization();
        if (! $org) {
            $org = SupplierOrganization::firstOrCreate(
                ['slug' => 'agritech-inputs-gujarat'],
                [
                    'name' => 'AgriTech Inputs Gujarat Pvt Ltd',
                    'category' => 'multi_category',
                    'description' => 'Manufacturer and distributor of certified hybrid seeds, micronutrients, bio-fertilizers, and farm equipment.',
                    'gst_number' => '24AAACA1234A1Z5',
                    'license_number' => 'GUJ/AGRI/DIST/2026/889',
                    'authorized_representative_name' => $user->name,
                    'contact_email' => $user->email,
                    'contact_phone' => $user->phone ?? '9876543220',
                    'operating_address' => 'GIDC Industrial Estate, Phase 2, Anand',
                    'district' => $user->district ?? 'Anand',
                    'state' => 'Gujarat',
                    'pincode' => $user->pincode ?? '388001',
                    'verification_status' => SupplierOrganization::STATUS_APPROVED,
                    'verified_at' => now(),
                ]
            );

            SupplierMember::firstOrCreate(
                ['supplier_organization_id' => $org->id, 'user_id' => $user->id],
                ['role_in_org' => 'owner', 'is_primary_contact' => true]
            );
        }

        return $org;
    }

    /**
     * Supplier Business Dashboard.
     */
    public function index()
    {
        $user = $this->getSupplierUser();
        $organization = $this->getOrCreateSupplierOrg($user);

        $teamMembers = $organization->supplierMembers()
            ->with('user')
            ->orderBy('id', 'asc')
            ->get();

        // Real product catalog stats
        $catalogCount = Product::count();
        $distributorNetworkCount = Distributor::count();

        $notifications = $user->notifications()->take(6)->get();

        return view('pages.supplier-dashboard', compact(
            'user',
            'organization',
            'teamMembers',
            'catalogCount',
            'distributorNetworkCount',
            'notifications'
        ));
    }

    /**
     * Update supplier business organization profile.
     */
    public function updateProfile(Request $request)
    {
        $user = $this->getSupplierUser();
        $organization = $this->getOrCreateSupplierOrg($user);

        // Authorization check via policy
        if ($user->id) {
            $this->authorize('update', $organization);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:2000'],
            'gst_number' => ['nullable', 'string', 'max:30'],
            'license_number' => ['nullable', 'string', 'max:50'],
            'authorized_representative_name' => ['required', 'string', 'max:255'],
            'contact_email' => ['required', 'email', 'max:255'],
            'contact_phone' => ['required', 'string', 'min:10', 'max:20'],
            'operating_address' => ['required', 'string', 'max:500'],
            'district' => ['required', 'string', 'max:100'],
            'state' => ['required', 'string', 'max:100'],
            'pincode' => ['nullable', 'string', 'max:10'],
        ]);

        $organization->update($validated);

        return redirect()->back()->with('role_message', 'Supplier organization profile updated successfully.');
    }

    /**
     * Add authorized team member to supplier business.
     */
    public function addMember(Request $request)
    {
        $user = $this->getSupplierUser();
        $organization = $this->getOrCreateSupplierOrg($user);

        if ($user->id) {
            $this->authorize('manageMembers', $organization);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'min:10', 'max:20'],
            'role_in_org' => ['required', 'string', 'in:manager,sales_rep,operations'],
        ]);

        $memberUser = User::firstOrCreate(
            ['email' => $validated['email']],
            [
                'name' => $validated['name'],
                'phone' => preg_replace('/[^0-9]/', '', $validated['phone']),
                'role' => 'supplier',
                'status' => 'active',
                'district' => $organization->district,
                'state' => $organization->state,
                'password' => Hash::make(Str::random(16)),
            ]
        );

        SupplierMember::updateOrCreate(
            ['supplier_organization_id' => $organization->id, 'user_id' => $memberUser->id],
            ['role_in_org' => $validated['role_in_org'], 'is_primary_contact' => false]
        );

        return redirect()->back()->with('role_message', "Team member '{$validated['name']}' added to supplier organization.");
    }

    /**
     * Remove authorized team member from supplier business.
     */
    public function removeMember(Request $request, SupplierMember $member)
    {
        $user = $this->getSupplierUser();
        $organization = $this->getOrCreateSupplierOrg($user);

        if ($user->id) {
            $this->authorize('manageMembers', $organization);
        }

        if ($member->supplier_organization_id !== $organization->id) {
            abort(403, 'Unauthorized action on another organization.');
        }

        if ($member->is_primary_contact) {
            return redirect()->back()->with('warning', 'Primary contact cannot be removed. Reassign primary contact first.');
        }

        $member->delete();

        return redirect()->back()->with('role_message', 'Team member removed from organization.');
    }
}
