<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Crop extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'en_name',
        'hi_name',
        'gu_name',
        'botanical_name',
        'category',
        'season',
        'icon',
        'badge',
        'optimal_temp',
        'pests',
        'mandi_price',
        'mandi_trend',
        'mandi_yard',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'pests' => 'array',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get localized crop name.
     */
    public function getLocalizedNameAttribute(): string
    {
        $locale = app()->getLocale();

        return match ($locale) {
            'gu' => $this->gu_name,
            'hi' => $this->hi_name,
            default => $this->en_name,
        };
    }
}
