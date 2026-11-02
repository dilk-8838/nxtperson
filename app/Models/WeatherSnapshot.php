<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeatherSnapshot extends Model
{
    use HasFactory;

    protected $fillable = [
        'district',
        'zone_name',
        'temperature',
        'condition',
        'humidity',
        'wind_speed',
        'wind_direction',
        'rainfall_probability',
        'spray_window',
        'soil_moisture',
        'heat_index',
        'forecast_json',
        'is_sample',
    ];

    protected function casts(): array
    {
        return [
            'forecast_json' => 'array',
            'temperature' => 'decimal:1',
            'is_sample' => 'boolean',
        ];
    }
}
