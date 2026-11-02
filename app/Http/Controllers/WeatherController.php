<?php

namespace App\Http\Controllers;

use App\Services\Weather\WeatherServiceInterface;
use Illuminate\Http\Request;

class WeatherController extends Controller
{
    /**
     * Display Gujarat Agricultural Weather Intelligence.
     */
    public function index(Request $request, WeatherServiceInterface $weatherService)
    {
        $districts = ['Rajkot', 'Anand', 'Banaskantha', 'Surat', 'Mehsana', 'Junagadh'];
        $selectedDistrict = $request->input('district', 'Rajkot');

        if (! in_array($selectedDistrict, $districts)) {
            $selectedDistrict = 'Rajkot';
        }

        $weather = $weatherService->getZoneWeather($selectedDistrict);

        // Hourly spray suitability timeline for next 12 hours
        $hourlySpray = [
            ['time' => '06:00 AM', 'temp' => '22°C', 'wind' => '6 km/h', 'humidity' => '65%', 'status' => 'Optimal', 'color' => 'emerald'],
            ['time' => '08:00 AM', 'temp' => '25°C', 'wind' => '8 km/h', 'humidity' => '60%', 'status' => 'Optimal', 'color' => 'emerald'],
            ['time' => '10:00 AM', 'temp' => '28°C', 'wind' => '11 km/h', 'humidity' => '52%', 'status' => 'Good', 'color' => 'emerald'],
            ['time' => '12:00 PM', 'temp' => '31°C', 'wind' => '14 km/h', 'humidity' => '45%', 'status' => 'Caution (High Temp)', 'color' => 'amber'],
            ['time' => '02:00 PM', 'temp' => '33°C', 'wind' => '16 km/h', 'humidity' => '40%', 'status' => 'Avoid (Evaporation)', 'color' => 'rose'],
            ['time' => '04:00 PM', 'temp' => '30°C', 'wind' => '12 km/h', 'humidity' => '48%', 'status' => 'Good', 'color' => 'emerald'],
            ['time' => '06:00 PM', 'temp' => '27°C', 'wind' => '9 km/h', 'humidity' => '55%', 'status' => 'Optimal', 'color' => 'emerald'],
        ];

        return view('pages.weather-full', compact('weather', 'districts', 'selectedDistrict', 'hourlySpray'));
    }
}
