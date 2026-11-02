<?php

namespace App\Http\Controllers;

use App\Models\BuyerProfile;
use App\Models\FarmerProfile;
use App\Models\FpoFarmerMembership;
use App\Models\FpoMember;
use App\Models\FpoOrganization;
use App\Models\SupplierMember;
use App\Models\SupplierOrganization;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DemoAuthController extends Controller
{
    /**
     * Show simulated demo login page.
     */
    public function showLogin()
    {
        return view('pages.login-preview');
    }

    /**
     * Switch user role in demo mode with full Laravel authentication.
     */
    public function switchRole(Request $request)
    {
        // Enforce demo mode check
        if (! config('app.demo_mode', true)) {
            abort(403, 'Demo role switching is disabled in production mode.');
        }

        $request->validate([
            'role' => 'required|in:farmer,supplier,distributor,buyer,fpo,cooperative,admin',
        ]);

        $role = $request->input('role');
        if ($role === 'cooperative') {
            $role = 'fpo';
        }

        $demoUsers = [
            'farmer' => [
                'name' => 'Suresh Patel (Demo Farmer)',
                'email' => 'suresh.patel@demo.nxtperson.farm',
                'phone' => '9876543210',
                'role' => 'farmer',
                'status' => 'active',
                'district' => 'Rajkot',
                'state' => 'Gujarat',
                'pincode' => '360001',
            ],
            'distributor' => [
                'name' => 'Kisan Seva Kendra (Mahesh Shah)',
                'email' => 'distributor.anand@demo.nxtperson.farm',
                'phone' => '9876543221',
                'role' => 'distributor',
                'status' => 'active',
                'district' => 'Anand',
                'state' => 'Gujarat',
                'pincode' => '388001',
            ],
            'supplier' => [
                'name' => 'AgriTech Inputs Gujarat (Demo Supplier)',
                'email' => 'supplier.agritech@demo.nxtperson.farm',
                'phone' => '9876543220',
                'role' => 'supplier',
                'status' => 'active',
                'district' => 'Anand',
                'state' => 'Gujarat',
                'pincode' => '388001',
            ],
            'buyer' => [
                'name' => 'Adani Agri Fresh / Saurashtra Agro (Demo Buyer)',
                'email' => 'buyer.procurement@demo.nxtperson.farm',
                'phone' => '9876543240',
                'role' => 'buyer',
                'status' => 'active',
                'district' => 'Ahmedabad',
                'state' => 'Gujarat',
                'pincode' => '380001',
            ],
            'fpo' => [
                'name' => 'Saurashtra Kisan Producer Co. (Demo FPO)',
                'email' => 'fpo.saurashtra@demo.nxtperson.farm',
                'phone' => '9876543250',
                'role' => 'fpo',
                'status' => 'active',
                'district' => 'Rajkot',
                'state' => 'Gujarat',
                'pincode' => '360001',
            ],
            'admin' => [
                'name' => 'Agronomic Systems Administrator (Demo Admin)',
                'email' => 'admin@demo.nxtperson.farm',
                'phone' => '9876543230',
                'role' => 'admin',
                'status' => 'active',
                'district' => 'Gandhinagar',
                'state' => 'Gujarat',
                'pincode' => '382010',
            ],
        ];

        $data = $demoUsers[$role];
        $user = User::where('email', $data['email'])->first();
        if ($user) {
            $user->update(['role' => $data['role']]);
        } else {
            $user = User::create(array_merge($data, ['password' => bcrypt('demo-password-123')]));
        }

        // Ensure attached profile/organization for demo
        if ($role === 'farmer' && ! $user->farmerProfile()->exists()) {
            FarmerProfile::create([
                'user_id' => $user->id,
                'farm_name' => 'Patel Bio-Farms (Gondal)',
                'village' => 'Gondal',
                'taluka' => 'Gondal',
                'district' => 'Rajkot',
                'state' => 'Gujarat',
                'pincode' => '360001',
                'total_acreage' => 12.7,
                'cultivated_acreage' => 12.7,
                'soil_type' => 'Deep Medium Black Cotton Soil',
                'irrigation_type' => 'drip',
                'crops_cultivated' => ['cotton', 'groundnut', 'wheat'],
                'expected_harvest_date' => now()->addDays(45)->toDateString(),
                'estimated_harvest_quantity' => 120,
                'estimated_harvest_unit' => 'Quintal',
                'kisan_credit_card' => 'KCC-GUJ-882910',
                'survey_number' => 'SRV-342/1-A',
                'verification_status' => 'verified',
                'verified_at' => now(),
            ]);
        } elseif ($role === 'supplier' && ! $user->supplierOrganizations()->exists()) {
            $org = SupplierOrganization::firstOrCreate(
                ['slug' => 'agritech-inputs-gujarat'],
                [
                    'name' => 'AgriTech Inputs Gujarat Pvt Ltd',
                    'category' => 'multi_category',
                    'description' => 'Direct manufacturer & distributor of certified hybrid seeds, micronutrients, bio-fertilizers, and farm equipment.',
                    'gst_number' => '24AAACA1234A1Z5',
                    'license_number' => 'GUJ/AGRI/DIST/2026/889',
                    'authorized_representative_name' => $user->name,
                    'contact_email' => $user->email,
                    'contact_phone' => $user->phone,
                    'operating_address' => 'GIDC Industrial Estate, Phase 2, Anand',
                    'district' => 'Anand',
                    'state' => 'Gujarat',
                    'pincode' => '388001',
                    'verification_status' => SupplierOrganization::STATUS_APPROVED,
                    'verified_at' => now(),
                ]
            );

            SupplierMember::firstOrCreate(
                ['supplier_organization_id' => $org->id, 'user_id' => $user->id],
                ['role_in_org' => 'owner', 'is_primary_contact' => true]
            );
        } elseif ($role === 'buyer' && ! $user->buyerProfile()->exists()) {
            BuyerProfile::create([
                'user_id' => $user->id,
                'company_name' => 'Adani Agri Fresh / Saurashtra Agro Processing Ltd',
                'buyer_type' => 'processor',
                'gst_number' => '24AAACA9876B1Z2',
                'pan_number' => 'AAACA9876B',
                'trade_license' => 'APMC-AHM-PROC-2026/102',
                'procurement_interests' => ['cotton', 'banana', 'potato', 'groundnut', 'cumin'],
                'annual_procurement_budget' => '₹50–100 Crore',
                'operating_address' => 'S.G. Highway Commercial Corridor, Ahmedabad',
                'district' => 'Ahmedabad',
                'state' => 'Gujarat',
                'pincode' => '380001',
                'verification_status' => 'verified',
                'verified_at' => now(),
            ]);
        } elseif ($role === 'fpo' && ! $user->fpoOrganizations()->exists()) {
            $fpo = FpoOrganization::firstOrCreate(
                ['slug' => 'saurashtra-kisan-producer-co'],
                [
                    'name' => 'Saurashtra Kisan Producer Company Ltd',
                    'organization_type' => 'Farmer Producer Company (FPC)',
                    'registration_number' => 'U01100GJ2023PTC140221',
                    'incorporation_year' => 2023,
                    'description' => 'NABARD-supported farmer producer organization aggregating 450+ smallholder cotton, groundnut, and cumin growers across Rajkot and Gondal.',
                    'registered_address' => 'Station Road, APMC Compound, Gondal',
                    'village' => 'Gondal',
                    'taluka' => 'Gondal',
                    'district' => 'Rajkot',
                    'state' => 'Gujarat',
                    'pincode' => '360311',
                    'authorized_representative_name' => $user->name,
                    'contact_email' => $user->email,
                    'contact_phone' => $user->phone,
                    'bank_name' => 'State Bank of India (Gondal Branch)',
                    'bank_account_number' => '38992010992',
                    'ifsc_code' => 'SBIN0000382',
                    'verification_status' => FpoOrganization::STATUS_APPROVED,
                    'verified_at' => now(),
                ]
            );

            FpoMember::firstOrCreate(
                ['fpo_organization_id' => $fpo->id, 'user_id' => $user->id],
                ['role_in_fpo' => 'admin', 'is_primary' => true]
            );

            // Link demo farmer to this FPO
            $demoFarmer = User::where('email', 'suresh.patel@demo.nxtperson.farm')->first();
            if ($demoFarmer) {
                FpoFarmerMembership::firstOrCreate(
                    ['fpo_organization_id' => $fpo->id, 'farmer_user_id' => $demoFarmer->id],
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

        Auth::login($user);
        session(['demo_role' => $role]);

        return redirect()->back()->with('role_message', "Active perspective switched to: {$user->name} (".ucfirst($role).' Mode)');
    }

    /**
     * Simulate sending an OTP to a farmer mobile number (demo only).
     */
    public function simulateSendOtp(Request $request)
    {
        if (! config('app.demo_mode', true)) {
            abort(403, 'Simulated OTP is only available in demo mode.');
        }

        $request->validate([
            'phone' => 'required|string|min:10|max:15',
        ]);

        $phone = preg_replace('/[^0-9]/', '', $request->input('phone'));
        $simulatedOtp = '123456';

        // Find or create farmer
        $user = User::firstOrCreate(
            ['phone' => $phone],
            [
                'name' => 'Gujarat Farmer ('.substr($phone, -4).')',
                'email' => "farmer.{$phone}@demo.nxtperson.farm",
                'role' => 'farmer',
                'district' => 'Rajkot',
                'state' => 'Gujarat',
                'pincode' => '360001',
            ]
        );

        $user->update([
            'otp_code' => $simulatedOtp,
            'otp_expires_at' => now()->addMinutes(15),
        ]);

        session(['simulated_phone' => $phone]);

        return redirect()->route('login')->with('otp_sent', "Demonstration Simulated OTP is: {$simulatedOtp}. Notice: No SMS was dispatched (Evaluation Mode).");
    }

    /**
     * Simulate verifying OTP and logging in the farmer.
     */
    public function simulateVerifyOtp(Request $request)
    {
        if (! config('app.demo_mode', true)) {
            abort(403, 'Simulated OTP is only available in demo mode.');
        }

        $request->validate([
            'phone' => 'required|string',
            'otp' => 'required|string',
        ]);

        $phone = preg_replace('/[^0-9]/', '', $request->input('phone'));
        $otp = trim($request->input('otp'));

        $user = User::where('phone', $phone)->first();

        // In demo mode, accept 123456 or matching OTP
        if ($otp === '123456' || ($user && $user->otp_code === $otp)) {
            if (! $user) {
                $user = User::create([
                    'name' => 'Suresh Patel (Rajkot Farmer)',
                    'phone' => $phone,
                    'email' => "farmer.{$phone}@demo.nxtperson.farm",
                    'role' => 'farmer',
                    'district' => 'Rajkot',
                    'state' => 'Gujarat',
                    'pincode' => '360001',
                ]);
            }

            Auth::login($user);
            session(['demo_role' => 'farmer']);

            return redirect()->route('home')->with('role_message', "Welcome, {$user->name}! Authenticated via Simulated Demonstration OTP.");
        }

        return redirect()->route('login')->withErrors(['otp' => 'Invalid OTP entered. For this demo, use: 123456']);
    }

    /**
     * Logout demo session.
     */
    public function logout()
    {
        Auth::logout();
        session()->forget('demo_role');

        return redirect()->route('home')->with('role_message', 'You have been logged out of the demonstration session.');
    }
}
