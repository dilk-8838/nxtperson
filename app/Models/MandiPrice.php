<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MandiPrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'crop_slug',
        'commodity_name',
        'variety',
        'yard_name',
        'district',
        'state',
        'arrival_quantity',
        'min_price',
        'modal_price',
        'max_price',
        'price_unit',
        'price_date',
        'trend_percentage',
        'is_sample',
    ];

    protected function casts(): array
    {
        return [
            'price_date' => 'date',
            'min_price' => 'decimal:2',
            'modal_price' => 'decimal:2',
            'max_price' => 'decimal:2',
            'is_sample' => 'boolean',
        ];
    }
}
