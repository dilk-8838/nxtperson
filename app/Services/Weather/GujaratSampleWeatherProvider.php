<?php

namespace App\Services\Weather;

use App\Models\WeatherSnapshot;

class GujaratSampleWeatherProvider implements WeatherServiceInterface
{
    /**
     * Curated Gujarat agro-climatic zones dictionary.
     */
    protected array $zonesData = [
        'Rajkot' => [
            'district' => 'Rajkot',
            'zone_name' => 'Saurashtra Central Agro-Climatic Zone (VI)',
            'temperature' => 28.5,
            'condition' => 'Scattered Clouds • Clear Horizons',
            'humidity' => 58,
            'wind_speed' => 11,
            'wind_direction' => 'NW',
            'rainfall_probability' => 12,
            'spray_window' => 'Optimal (Next 36 Hours)',
            'soil_moisture' => 'Adequate (64% VWC)',
            'heat_index' => '30°C',
            'is_sample' => true,
            'forecast_json' => [
                ['day' => 'Today', 'temp' => '31° / 21°', 'icon' => '⛅', 'spray' => 'Good', 'pop' => '10%', 'wind' => '11 km/h'],
                ['day' => 'Tomorrow', 'temp' => '32° / 20°', 'icon' => '☀️', 'spray' => 'Optimal', 'pop' => '5%', 'wind' => '9 km/h'],
                ['day' => 'Saturday', 'temp' => '33° / 22°', 'icon' => '🌤️', 'spray' => 'Optimal', 'pop' => '5%', 'wind' => '12 km/h'],
                ['day' => 'Sunday', 'temp' => '30° / 21°', 'icon' => '🌦️', 'spray' => 'Moderate', 'pop' => '35%', 'wind' => '16 km/h'],
                ['day' => 'Monday', 'temp' => '29° / 20°', 'icon' => '⛅', 'spray' => 'Good', 'pop' => '15%', 'wind' => '14 km/h'],
                ['day' => 'Tuesday', 'temp' => '31° / 21°', 'icon' => '☀️', 'spray' => 'Optimal', 'pop' => '5%', 'wind' => '10 km/h'],
                ['day' => 'Wednesday', 'temp' => '32° / 22°', 'icon' => '☀️', 'spray' => 'Optimal', 'pop' => '5%', 'wind' => '8 km/h'],
            ],
        ],
        'Anand' => [
            'district' => 'Anand',
            'zone_name' => 'Middle Gujarat Agro-Climatic Zone (III)',
            'temperature' => 30.2,
            'condition' => 'Warm & Humid • Low Wind',
            'humidity' => 68,
            'wind_speed' => 7,
            'wind_direction' => 'SW',
            'rainfall_probability' => 18,
            'spray_window' => 'Favorable Morning Spray Window',
            'soil_moisture' => 'High (72% VWC - Canal Command)',
            'heat_index' => '33°C',
            'is_sample' => true,
            'forecast_json' => [
                ['day' => 'Today', 'temp' => '32° / 22°', 'icon' => '🌤️', 'spray' => 'Good', 'pop' => '15%', 'wind' => '7 km/h'],
                ['day' => 'Tomorrow', 'temp' => '33° / 23°', 'icon' => '⛅', 'spray' => 'Optimal', 'pop' => '10%', 'wind' => '8 km/h'],
                ['day' => 'Saturday', 'temp' => '34° / 23°', 'icon' => '☀️', 'spray' => 'Optimal', 'pop' => '5%', 'wind' => '9 km/h'],
                ['day' => 'Sunday', 'temp' => '32° / 22°', 'icon' => '🌦️', 'spray' => 'Caution', 'pop' => '40%', 'wind' => '14 km/h'],
                ['day' => 'Monday', 'temp' => '31° / 21°', 'icon' => '⛅', 'spray' => 'Good', 'pop' => '20%', 'wind' => '10 km/h'],
                ['day' => 'Tuesday', 'temp' => '32° / 22°', 'icon' => '☀️', 'spray' => 'Optimal', 'pop' => '5%', 'wind' => '8 km/h'],
                ['day' => 'Wednesday', 'temp' => '33° / 23°', 'icon' => '☀️', 'spray' => 'Optimal', 'pop' => '5%', 'wind' => '7 km/h'],
            ],
        ],
        'Banaskantha' => [
            'district' => 'Banaskantha',
            'zone_name' => 'North Gujarat Semi-Arid Zone (IV)',
            'temperature' => 27.0,
            'condition' => 'Dry & Clear • High UV',
            'humidity' => 42,
            'wind_speed' => 14,
            'wind_direction' => 'NE',
            'rainfall_probability' => 5,
            'spray_window' => 'Safe Spray (Low Drift Risk)',
            'soil_moisture' => 'Moderate (48% VWC - Sandy Loam)',
            'heat_index' => '28°C',
            'is_sample' => true,
            'forecast_json' => [
                ['day' => 'Today', 'temp' => '30° / 18°', 'icon' => '☀️', 'spray' => 'Optimal', 'pop' => '0%', 'wind' => '14 km/h'],
                ['day' => 'Tomorrow', 'temp' => '31° / 17°', 'icon' => '☀️', 'spray' => 'Optimal', 'pop' => '0%', 'wind' => '12 km/h'],
                ['day' => 'Saturday', 'temp' => '32° / 19°', 'icon' => '🌤️', 'spray' => 'Optimal', 'pop' => '5%', 'wind' => '11 km/h'],
                ['day' => 'Sunday', 'temp' => '31° / 18°', 'icon' => '☀️', 'spray' => 'Optimal', 'pop' => '5%', 'wind' => '13 km/h'],
                ['day' => 'Monday', 'temp' => '30° / 18°', 'icon' => '☀️', 'spray' => 'Optimal', 'pop' => '0%', 'wind' => '15 km/h'],
                ['day' => 'Tuesday', 'temp' => '29° / 17°', 'icon' => '☀️', 'spray' => 'Optimal', 'pop' => '0%', 'wind' => '12 km/h'],
                ['day' => 'Wednesday', 'temp' => '30° / 18°', 'icon' => '🌤️', 'spray' => 'Optimal', 'pop' => '5%', 'wind' => '10 km/h'],
            ],
        ],
        'Surat' => [
            'district' => 'Surat',
            'zone_name' => 'South Gujarat Coastal Heavy Rainfall Zone (I)',
            'temperature' => 31.4,
            'condition' => 'Humid Coastal Breeze • Partly Overcast',
            'humidity' => 74,
            'wind_speed' => 16,
            'wind_direction' => 'W',
            'rainfall_probability' => 28,
            'spray_window' => 'Caution: Moderate Wind Drift',
            'soil_moisture' => 'Saturated (82% VWC)',
            'heat_index' => '36°C',
            'is_sample' => true,
            'forecast_json' => [
                ['day' => 'Today', 'temp' => '32° / 25°', 'icon' => '⛅', 'spray' => 'Caution', 'pop' => '25%', 'wind' => '16 km/h'],
                ['day' => 'Tomorrow', 'temp' => '31° / 24°', 'icon' => '🌦️', 'spray' => 'Avoid', 'pop' => '45%', 'wind' => '18 km/h'],
                ['day' => 'Saturday', 'temp' => '32° / 24°', 'icon' => '⛅', 'spray' => 'Good', 'pop' => '20%', 'wind' => '14 km/h'],
                ['day' => 'Sunday', 'temp' => '33° / 25°', 'icon' => '🌤️', 'spray' => 'Good', 'pop' => '15%', 'wind' => '12 km/h'],
                ['day' => 'Monday', 'temp' => '32° / 24°', 'icon' => '⛅', 'spray' => 'Caution', 'pop' => '30%', 'wind' => '15 km/h'],
                ['day' => 'Tuesday', 'temp' => '33° / 25°', 'icon' => '☀️', 'spray' => 'Optimal', 'pop' => '10%', 'wind' => '11 km/h'],
                ['day' => 'Wednesday', 'temp' => '32° / 24°', 'icon' => '🌤️', 'spray' => 'Optimal', 'pop' => '10%', 'wind' => '10 km/h'],
            ],
        ],
        'Mehsana' => [
            'district' => 'Mehsana',
            'zone_name' => 'North Gujarat Tube-well Irrigated Zone (V)',
            'temperature' => 29.1,
            'condition' => 'Clear Sunshine • Low Humidity',
            'humidity' => 46,
            'wind_speed' => 10,
            'wind_direction' => 'N',
            'rainfall_probability' => 8,
            'spray_window' => 'Optimal Foliar Spray Conditions',
            'soil_moisture' => 'Moderate (54% VWC)',
            'heat_index' => '30°C',
            'is_sample' => true,
            'forecast_json' => [
                ['day' => 'Today', 'temp' => '31° / 19°', 'icon' => '☀️', 'spray' => 'Optimal', 'pop' => '5%', 'wind' => '10 km/h'],
                ['day' => 'Tomorrow', 'temp' => '32° / 19°', 'icon' => '☀️', 'spray' => 'Optimal', 'pop' => '0%', 'wind' => '9 km/h'],
                ['day' => 'Saturday', 'temp' => '33° / 20°', 'icon' => '🌤️', 'spray' => 'Optimal', 'pop' => '5%', 'wind' => '11 km/h'],
                ['day' => 'Sunday', 'temp' => '31° / 19°', 'icon' => '⛅', 'spray' => 'Good', 'pop' => '15%', 'wind' => '13 km/h'],
                ['day' => 'Monday', 'temp' => '30° / 18°', 'icon' => '☀️', 'spray' => 'Optimal', 'pop' => '5%', 'wind' => '12 km/h'],
                ['day' => 'Tuesday', 'temp' => '31° / 19°', 'icon' => '☀️', 'spray' => 'Optimal', 'pop' => '0%', 'wind' => '8 km/h'],
                ['day' => 'Wednesday', 'temp' => '32° / 20°', 'icon' => '☀️', 'spray' => 'Optimal', 'pop' => '0%', 'wind' => '9 km/h'],
            ],
        ],
        'Junagadh' => [
            'district' => 'Junagadh',
            'zone_name' => 'South Saurashtra Agro-Climatic Zone (VII)',
            'temperature' => 29.8,
            'condition' => 'Coastal Sunshine • Mountain Breeze (Girnar)',
            'humidity' => 62,
            'wind_speed' => 12,
            'wind_direction' => 'SW',
            'rainfall_probability' => 14,
            'spray_window' => 'Optimal (Next 24 Hours)',
            'soil_moisture' => 'Adequate (66% VWC)',
            'heat_index' => '31°C',
            'is_sample' => true,
            'forecast_json' => [
                ['day' => 'Today', 'temp' => '31° / 22°', 'icon' => '🌤️', 'spray' => 'Optimal', 'pop' => '10%', 'wind' => '12 km/h'],
                ['day' => 'Tomorrow', 'temp' => '32° / 21°', 'icon' => '☀️', 'spray' => 'Optimal', 'pop' => '5%', 'wind' => '10 km/h'],
                ['day' => 'Saturday', 'temp' => '33° / 22°', 'icon' => '☀️', 'spray' => 'Optimal', 'pop' => '5%', 'wind' => '11 km/h'],
                ['day' => 'Sunday', 'temp' => '30° / 21°', 'icon' => '🌦️', 'spray' => 'Moderate', 'pop' => '30%', 'wind' => '15 km/h'],
                ['day' => 'Monday', 'temp' => '29° / 20°', 'icon' => '⛅', 'spray' => 'Good', 'pop' => '15%', 'wind' => '13 km/h'],
                ['day' => 'Tuesday', 'temp' => '31° / 21°', 'icon' => '☀️', 'spray' => 'Optimal', 'pop' => '5%', 'wind' => '9 km/h'],
                ['day' => 'Wednesday', 'temp' => '32° / 22°', 'icon' => '☀️', 'spray' => 'Optimal', 'pop' => '5%', 'wind' => '10 km/h'],
            ],
        ],
    ];

    /**
     * Get current weather snapshot for a Gujarat agro zone or district.
     */
    public function getZoneWeather(string $district = 'Rajkot'): WeatherSnapshot
    {
        $normalized = ucfirst(strtolower(trim($district)));

        // Try exact database query first
        $snapshot = WeatherSnapshot::where('district', 'like', "%{$normalized}%")->first();
        if ($snapshot) {
            return $snapshot;
        }

        // Check curated zones
        $data = $this->zonesData[$normalized] ?? $this->zonesData['Rajkot'];

        return WeatherSnapshot::updateOrCreate(
            ['district' => $data['district']],
            $data
        );
    }

    /**
     * Get 7-day spray advisory and forecast.
     */
    public function getSprayAdvisory(string $district = 'Rajkot'): array
    {
        $weather = $this->getZoneWeather($district);

        return [
            'district' => $weather->district,
            'zone_name' => $weather->zone_name,
            'status' => $weather->spray_window,
            'wind' => $weather->wind_speed.' km/h '.$weather->wind_direction,
            'humidity' => $weather->humidity.'%',
            'soil_moisture' => $weather->soil_moisture,
            'heat_index' => $weather->heat_index,
            'is_demonstration' => true,
            'notice' => 'Sample Gujarat agricultural weather data for demonstration.',
            'forecast' => $weather->forecast_json ?? [],
        ];
    }
}
