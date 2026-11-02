<?php

namespace App\Services\Weather;

use App\Models\WeatherSnapshot;

interface WeatherServiceInterface
{
    /**
     * Get current weather snapshot for a Gujarat agro zone or district.
     */
    public function getZoneWeather(string $district = 'Rajkot'): WeatherSnapshot;

    /**
     * Get 7-day spray advisory and forecast.
     */
    public function getSprayAdvisory(string $district = 'Rajkot'): array;
}
