<?php

namespace App\Http\Controllers;

use App\Models\Advisory;
use App\Models\FarmerProfile;
use App\Models\MandiPrice;
use App\Models\ProduceLot;
use App\Models\ProduceReservation;
use App\Models\User;
use App\Services\Weather\WeatherServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FarmerDashboardController extends Controller
{
    public function __construct(
        protected WeatherServiceInterface $weatherService
    ) {}

    /**
     * Resolve the active farmer user (handles demo fallback).
     */
    protected function getFarmerUser(): User
    {
        $user = Auth::user();
        if ($user && $user->isFarmer()) {
            return $user;
        }

        // Demo fallback
        return User::firstOrCreate(
            ['email' => 'suresh.patel@demo.nxtperson.farm'],
            [
                'name' => 'Suresh Patel (Demo Farmer)',
                'phone' => '9876543210',
                'role' => 'farmer',
                'status' => 'active',
                'district' => 'Rajkot',
                'state' => 'Gujarat',
                'pincode' => '360001',
                'password' => bcrypt('demo1234'),
            ]
        );
    }

    /**
     * Display the comprehensive Farmer Portal dashboard.
     */
    public function index()
    {
        $user = $this->getFarmerUser();
        $district = $user->district ?? 'Rajkot';

        // 1. Persisted Farmer Profile
        $profile = $user->farmerProfile;
        if (! $profile) {
            $profile = FarmerProfile::create([
                'user_id' => $user->id,
                'farm_name' => "{$user->name}'s Farm",
                'village' => 'Gondal',
                'taluka' => 'Gondal',
                'district' => $district,
                'state' => $user->state ?? 'Gujarat',
                'pincode' => $user->pincode ?? '360001',
                'total_acreage' => 12.5,
                'cultivated_acreage' => 12.5,
                'soil_type' => 'Deep Medium Black Cotton Soil',
                'irrigation_type' => 'drip',
                'crops_cultivated' => ['cotton', 'groundnut', 'wheat'],
                'expected_harvest_date' => now()->addDays(40)->toDateString(),
                'estimated_harvest_quantity' => 120,
                'estimated_harvest_unit' => 'Quintal',
                'verification_status' => 'verified',
                'verified_at' => now(),
            ]);
        }

        // 2. Real Persisted Produce Lots
        $lots = ProduceLot::with(['crop', 'primaryImage', 'latestAssessment'])
            ->where('farmer_id', $user->id)
            ->orderBy('id', 'desc')
            ->take(6)
            ->get();

        $lotStats = [
            'total_lots' => ProduceLot::where('farmer_id', $user->id)->count(),
            'approved_lots' => ProduceLot::where('farmer_id', $user->id)->whereIn('status', [
                ProduceLot::STATUS_APPROVED_FOR_PROCUREMENT,
                ProduceLot::STATUS_PURCHASE_CONFIRMED,
                ProduceLot::STATUS_INVENTORY_AVAILABLE,
                ProduceLot::STATUS_PARTIALLY_SOLD,
            ])->count(),
            'total_quantity_kg' => (float) ProduceLot::where('farmer_id', $user->id)->sum('quantity_in_kg'),
        ];

        // 3. Real Persisted Reservations on this farmer's produce lots
        $lotIds = ProduceLot::where('farmer_id', $user->id)->pluck('id');
        $reservations = ProduceReservation::with(['buyer', 'lot.crop'])
            ->whereIn('produce_lot_id', $lotIds)
            ->orderBy('id', 'desc')
            ->take(5)
            ->get();

        // 4. Linked FPO / Cooperative Affiliations
        $fpos = $user->associatedFpos()->get();

        // 5. Farmer's Advisory Tickets
        $advisories = Advisory::where('farmer_phone', $user->phone)
            ->orWhere('farmer_district', $district)
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        // 6. Localized Gujarat Agro-Weather Telemetry
        $weather = $this->weatherService->getZoneWeather($district);

        // 7. APMC Mandi Rates for farmer's cultivated crops
        $cultivatedCrops = $profile->crops_cultivated ?? ['cotton', 'groundnut'];
        $mandiPrices = MandiPrice::whereIn('crop_slug', $cultivatedCrops)
            ->orderBy('modal_price', 'desc')
            ->take(6)
            ->get();

        if ($mandiPrices->isEmpty()) {
            $mandiPrices = MandiPrice::orderBy('modal_price', 'desc')->take(6)->get();
        }

        $plots = [
            [
                'name' => 'Kharif Cotton Plot A',
                'crop' => 'Shankar-6 Cotton',
                'crop_slug' => 'cotton',
                'area' => '8.5 Acres',
                'village' => $profile->village ?? 'Gondal',
                'soil' => $profile->soil_type ?? 'Medium Black Cotton',
                'stage' => 'Boll Formation Stage',
                'health' => 'NDVI 0.76 (Good)',
                'health_badge' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
                'icon' => '☁️',
            ],
            [
                'name' => 'Plot 2 (Canal Basin)',
                'crop' => 'GG-20 Groundnut',
                'crop_slug' => 'groundnut',
                'area' => '5.0 Acres',
                'village' => $profile->village ?? 'Gondal',
                'soil' => 'Sandy Loam',
                'stage' => 'Pegging Phase',
                'health' => 'NDVI 0.69 (Moderate)',
                'health_badge' => 'bg-amber-100 text-amber-800 border-amber-300',
                'icon' => '🥜',
            ],
            [
                'name' => 'Plot 3 (Borewell Belt)',
                'crop' => 'Cumin (GC-4)',
                'crop_slug' => 'grain',
                'area' => '5.0 Acres',
                'village' => $profile->village ?? 'Gondal',
                'soil' => 'Medium Black Soil',
                'stage' => 'Vegetative Canopy',
                'health' => 'NDVI 0.81 (Vibrant)',
                'health_badge' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
                'icon' => '🌾',
            ],
        ];

        return view('pages.farmer-dashboard', compact(
            'user',
            'profile',
            'district',
            'plots',
            'lots',
            'lotStats',
            'reservations',
            'fpos',
            'advisories',
            'weather',
            'mandiPrices'
        ));
    }

    /**
     * Update farmer farm profile.
     */
    public function updateProfile(Request $request)
    {
        $user = $this->getFarmerUser();
        $profile = $user->farmerProfile;

        if ($profile) {
            $this->authorize('update', $profile);
        }

        $validated = $request->validate([
            'farm_name' => 'required|string|max:255',
            'village' => 'nullable|string|max:100',
            'taluka' => 'nullable|string|max:100',
            'district' => 'required|string|max:100',
            'pincode' => 'nullable|string|max:10',
            'total_acreage' => 'required|numeric|min:0.1|max:1000',
            'cultivated_acreage' => 'nullable|numeric|min:0|max:1000',
            'soil_type' => 'nullable|string|max:100',
            'irrigation_type' => 'nullable|string|max:50',
            'crops_cultivated' => 'nullable|array',
            'crops_cultivated.*' => 'string|max:50',
            'expected_harvest_date' => 'nullable|date',
            'estimated_harvest_quantity' => 'nullable|numeric|min:0',
            'estimated_harvest_unit' => 'nullable|string|max:20',
            'kisan_credit_card' => 'nullable|string|max:50',
            'survey_number' => 'nullable|string|max:50',
        ]);

        if (! $profile) {
            $profile = new FarmerProfile(['user_id' => $user->id]);
        }

        $profile->fill($validated);
        $profile->save();

        // Also sync district/pincode to user
        $user->update([
            'district' => $validated['district'],
            'pincode' => $validated['pincode'] ?? $user->pincode,
        ]);

        return redirect()->back()->with('role_message', 'Farmer land and crop profile updated successfully.');
    }
}
