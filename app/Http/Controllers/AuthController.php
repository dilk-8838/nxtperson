<?php

namespace App\Http\Controllers;

use App\Models\BuyerProfile;
use App\Models\FarmerProfile;
use App\Models\FpoMember;
use App\Models\FpoOrganization;
use App\Models\SupplierMember;
use App\Models\SupplierOrganization;
use App\Models\User;
use App\Notifications\FpoRegistrationSubmittedNotification;
use App\Notifications\SupplierRegistrationSubmittedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    /**
     * Show role-specific registration portal.
     */
    public function showRegister(Request $request, ?string $role = null)
    {
        $role = $role ?? $request->query('role', 'farmer');
        if (! in_array($role, ['farmer', 'supplier', 'buyer', 'fpo'])) {
            $role = 'farmer';
        }

        return view('pages.auth.register', compact('role'));
    }

    /**
     * Process new user and organization registration.
     * Enforces strict role validation to prevent privilege escalation (cannot register as admin).
     */
    public function register(Request $request)
    {
        // Strict role validation - admin role is explicitly forbidden from self-registration
        $validated = $request->validate([
            'role' => ['required', 'string', Rule::in(['farmer', 'supplier', 'buyer', 'fpo'])],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'min:10', 'max:15', 'unique:users,phone'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'district' => ['required', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'pincode' => ['nullable', 'string', 'max:10'],

            // Role-specific optional fields
            // Farmer fields
            'farm_name' => ['nullable', 'string', 'max:255'],
            'village' => ['nullable', 'string', 'max:100'],
            'total_acreage' => ['nullable', 'numeric', 'min:0'],
            'soil_type' => ['nullable', 'string', 'max:100'],
            'irrigation_type' => ['nullable', 'string', 'max:50'],
            'crops_cultivated' => ['nullable', 'array'],

            // Supplier fields
            'business_name' => ['required_if:role,supplier', 'nullable', 'string', 'max:255'],
            'business_category' => ['required_if:role,supplier', 'nullable', 'string', 'max:50'],
            'gst_number' => ['nullable', 'string', 'max:30'],
            'license_number' => ['nullable', 'string', 'max:50'],
            'operating_address' => ['required_if:role,supplier,fpo', 'nullable', 'string', 'max:500'],
            'business_description' => ['nullable', 'string', 'max:2000'],

            // Buyer fields
            'company_name' => ['nullable', 'string', 'max:255'],
            'buyer_type' => ['nullable', 'string', 'max:50'],

            // FPO fields
            'fpo_name' => ['required_if:role,fpo', 'nullable', 'string', 'max:255'],
            'organization_type' => ['required_if:role,fpo', 'nullable', 'string', 'max:100'],
            'registration_number' => ['required_if:role,fpo', 'nullable', 'string', 'max:50'],
            'incorporation_year' => ['nullable', 'integer', 'min:1950', 'max:'.date('Y')],
        ]);

        return DB::transaction(function () use ($validated) {
            $userRole = $validated['role'];
            $userStatus = 'active';

            // Create base User record
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => preg_replace('/[^0-9]/', '', $validated['phone']),
                'role' => $userRole,
                'status' => $userStatus,
                'district' => $validated['district'],
                'state' => $validated['state'] ?? 'Gujarat',
                'pincode' => $validated['pincode'] ?? null,
                'password' => Hash::make($validated['password']),
            ]);

            // Handle role-specific profiles and organizations
            if ($userRole === 'farmer') {
                FarmerProfile::create([
                    'user_id' => $user->id,
                    'farm_name' => $validated['farm_name'] ?? ($user->name."'s Farm"),
                    'village' => $validated['village'] ?? null,
                    'district' => $user->district,
                    'state' => $user->state,
                    'pincode' => $user->pincode,
                    'total_acreage' => $validated['total_acreage'] ?? 0,
                    'cultivated_acreage' => $validated['total_acreage'] ?? 0,
                    'soil_type' => $validated['soil_type'] ?? 'Medium Black Cotton Soil',
                    'irrigation_type' => $validated['irrigation_type'] ?? 'borewell',
                    'crops_cultivated' => $validated['crops_cultivated'] ?? ['cotton'],
                    'verification_status' => 'verified',
                    'verified_at' => now(),
                ]);

                Auth::login($user);
                session(['demo_role' => 'farmer']);

                return redirect()->route('dashboard')->with('role_message', 'Welcome to NxtPerson Agritech! Your farmer profile has been established.');
            }

            if ($userRole === 'supplier') {
                $supplierOrg = SupplierOrganization::create([
                    'name' => $validated['business_name'] ?? ($user->name.' Inputs'),
                    'slug' => Str::slug($validated['business_name'] ?? ($user->name.' Inputs')).'-'.Str::random(4),
                    'category' => $validated['business_category'] ?? 'seeds',
                    'description' => $validated['business_description'] ?? 'Registered supplier of certified agricultural inputs.',
                    'gst_number' => $validated['gst_number'] ?? null,
                    'license_number' => $validated['license_number'] ?? null,
                    'authorized_representative_name' => $user->name,
                    'contact_email' => $user->email,
                    'contact_phone' => $user->phone,
                    'operating_address' => $validated['operating_address'] ?? ($user->district.', Gujarat'),
                    'district' => $user->district,
                    'state' => $user->state,
                    'pincode' => $user->pincode,
                    'verification_status' => SupplierOrganization::STATUS_PENDING,
                ]);

                SupplierMember::create([
                    'supplier_organization_id' => $supplierOrg->id,
                    'user_id' => $user->id,
                    'role_in_org' => 'owner',
                    'is_primary_contact' => true,
                ]);

                // Notify admin of new supplier submission
                $admins = User::where('role', 'admin')->get();
                foreach ($admins as $admin) {
                    $admin->notify(new SupplierRegistrationSubmittedNotification($supplierOrg));
                }

                Auth::login($user);
                session(['demo_role' => 'supplier']);

                return redirect()->route('supplier.dashboard')->with('role_message', "Supplier registration submitted for '{$supplierOrg->name}'. Account is under administrative verification.");
            }

            if ($userRole === 'buyer') {
                BuyerProfile::create([
                    'user_id' => $user->id,
                    'company_name' => $validated['company_name'] ?? ($user->name.' Procurement'),
                    'buyer_type' => $validated['buyer_type'] ?? 'processor',
                    'gst_number' => $validated['gst_number'] ?? null,
                    'operating_address' => $validated['operating_address'] ?? ($user->district.', Gujarat'),
                    'district' => $user->district,
                    'state' => $user->state,
                    'pincode' => $user->pincode,
                    'verification_status' => 'verified',
                    'verified_at' => now(),
                ]);

                Auth::login($user);
                session(['demo_role' => 'buyer']);

                return redirect()->route('buyer.dashboard')->with('role_message', 'Welcome to NxtPerson Agritech! Your buyer procurement account is ready.');
            }

            if ($userRole === 'fpo') {
                $fpoOrg = FpoOrganization::create([
                    'name' => $validated['fpo_name'] ?? ($user->name.' Cooperative'),
                    'slug' => Str::slug($validated['fpo_name'] ?? ($user->name.' Cooperative')).'-'.Str::random(4),
                    'organization_type' => $validated['organization_type'] ?? 'Farmer Producer Company (FPC)',
                    'registration_number' => $validated['registration_number'] ?? ('CIN-'.strtoupper(Str::random(12))),
                    'incorporation_year' => $validated['incorporation_year'] ?? date('Y'),
                    'description' => $validated['business_description'] ?? 'Farmer producer organization aggregating agricultural produce.',
                    'registered_address' => $validated['operating_address'] ?? ($user->district.', Gujarat'),
                    'village' => $validated['village'] ?? null,
                    'district' => $user->district,
                    'state' => $user->state,
                    'pincode' => $user->pincode,
                    'authorized_representative_name' => $user->name,
                    'contact_email' => $user->email,
                    'contact_phone' => $user->phone,
                    'verification_status' => FpoOrganization::STATUS_PENDING,
                ]);

                FpoMember::create([
                    'fpo_organization_id' => $fpoOrg->id,
                    'user_id' => $user->id,
                    'role_in_fpo' => 'admin',
                    'is_primary' => true,
                ]);

                // Notify admin of new FPO submission
                $admins = User::where('role', 'admin')->get();
                foreach ($admins as $admin) {
                    $admin->notify(new FpoRegistrationSubmittedNotification($fpoOrg));
                }

                Auth::login($user);
                session(['demo_role' => 'fpo']);

                return redirect()->route('fpo.dashboard')->with('role_message', "FPO registration submitted for '{$fpoOrg->name}'. Account is under administrative verification.");
            }

            Auth::login($user);

            return redirect()->route('home');
        });
    }

    /**
     * Process standard login.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $login = $credentials['login'];
        $user = User::where('email', $login)
            ->orWhere('phone', preg_replace('/[^0-9]/', '', $login))
            ->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return back()->withInput($request->only('login'))->withErrors([
                'login' => 'Invalid email/phone or password credentials entered.',
            ]);
        }

        if ($user->isSuspended()) {
            return back()->withErrors([
                'login' => 'Your account has been suspended by administration. Please contact support.',
            ]);
        }

        Auth::login($user, $request->boolean('remember'));

        // Map role to session demo role for seamless compatibility
        $mappedRole = match ($user->role) {
            'distributor' => 'supplier',
            'cooperative' => 'fpo',
            default => $user->role,
        };
        session(['demo_role' => $mappedRole]);

        return match ($mappedRole) {
            'admin' => redirect()->route('admin.index')->with('role_message', "Welcome, Administrator {$user->name}."),
            'supplier' => redirect()->route('supplier.dashboard')->with('role_message', "Welcome back, {$user->name}."),
            'buyer' => redirect()->route('buyer.dashboard')->with('role_message', "Welcome back, {$user->name}."),
            'fpo' => redirect()->route('fpo.dashboard')->with('role_message', "Welcome back, {$user->name}."),
            default => redirect()->route('dashboard')->with('role_message', "Welcome back, {$user->name}."),
        };
    }
}
