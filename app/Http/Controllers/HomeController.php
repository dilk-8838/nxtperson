<?php

namespace App\Http\Controllers;

use App\Models\Advisory;
use App\Models\Crop;
use App\Services\Advisory\CropAdvisoryEngineInterface;
use App\Services\Mandi\MandiPriceServiceInterface;
use App\Services\Weather\WeatherServiceInterface;

class HomeController extends Controller
{
    /**
     * Show the NxtPerson responsive agricultural technology demo homepage.
     * Fully database-driven with modular service providers.
     */
    public function index(
        WeatherServiceInterface $weatherService,
        MandiPriceServiceInterface $mandiService,
        CropAdvisoryEngineInterface $advisoryEngine
    ) {
        $currentLocale = app()->getLocale();
        $currentRole = session('demo_role', 'farmer');

        // 1. Fetch 8 Key Gujarat Crops from Database
        $crops = Crop::where('is_active', true)->get();

        // 2. Fetch Live APMC Mandi Rates from Database
        $mandiRates = $mandiService->getLatestPrices();

        // 3. Fetch Localized Weather & Spray Snapshot
        $weatherModel = $weatherService->getZoneWeather('Rajkot');
        $weatherSummary = [
            'district' => $weatherModel->district.' & '.$weatherModel->zone_name,
            'temperature' => (int) $weatherModel->temperature,
            'condition' => $weatherModel->condition,
            'humidity' => $weatherModel->humidity,
            'wind_speed' => $weatherModel->wind_speed,
            'wind_dir' => $weatherModel->wind_direction,
            'rainfall_prob' => $weatherModel->rainfall_probability,
            'spray_window' => $weatherModel->spray_window,
            'soil_moisture' => $weatherModel->soil_moisture,
            'heat_index' => $weatherModel->heat_index,
            'next_forecast' => $weatherModel->forecast_json ?? [],
        ];

        // 4. Fetch Seeded Demonstration Advisories
        $recentAdvisories = Advisory::latest()->take(3)->get();

        // 5. Diagnostic rules for interactive scanner
        $diagnosticRules = $advisoryEngine->getRules();

        return view('home', compact(
            'crops',
            'mandiRates',
            'weatherSummary',
            'recentAdvisories',
            'diagnosticRules',
            'currentLocale',
            'currentRole'
        ));
    }
}
