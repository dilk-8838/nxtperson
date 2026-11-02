<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Distributor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'business_name',
        'license_number',
        'district',
        'taluka',
        'address',
        'pincode',
        'phone',
        'whatsapp',
        'authorized_categories',
        'featured_brands',
        'is_verified',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'authorized_categories' => 'array',
            'featured_brands' => 'array',
            'is_verified' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'distributor_product')
            ->withPivot('in_stock')
            ->withTimestamps();
    }
}
