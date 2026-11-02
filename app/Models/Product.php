<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'manufacturer_id',
        'supplier_organization_id',
        'category_slug',
        'name',
        'brand_name',
        'slug',
        'active_ingredient',
        'package_size',
        'unit',
        'unit_price',
        'min_order_quantity',
        'stock_quantity',
        'price_guide',
        'description',
        'specifications',
        'usage_instructions',
        'safety_guidelines',
        'target_crops',
        'image_url',
        'is_featured',
        'is_active',
        'is_available',
    ];

    protected function casts(): array
    {
        return [
            'target_crops' => 'array',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'is_available' => 'boolean',
            'unit_price' => 'decimal:2',
            'min_order_quantity' => 'decimal:2',
            'stock_quantity' => 'decimal:2',
        ];
    }

    public function manufacturer(): BelongsTo
    {
        return $this->belongsTo(Manufacturer::class);
    }

    public function supplierOrganization(): BelongsTo
    {
        return $this->belongsTo(SupplierOrganization::class, 'supplier_organization_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'category_slug', 'slug');
    }

    public function distributors(): BelongsToMany
    {
        return $this->belongsToMany(Distributor::class, 'distributor_product')
            ->withPivot('in_stock')
            ->withTimestamps();
    }

    public function isAvailable(): bool
    {
        return ($this->is_active ?? true) && ($this->is_available ?? true) && ($this->stock_quantity > 0);
    }
}
