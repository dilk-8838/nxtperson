<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FpoCollectiveListing extends Model
{
    use HasFactory;

    public const STATUS_DRAFT = 'draft';

    public const STATUS_PUBLISHED = 'published';

    public const STATUS_PARTIALLY_SOLD = 'partially_sold';

    public const STATUS_SOLD_OUT = 'sold_out';

    public const STATUS_WITHDRAWN = 'withdrawn';

    protected $fillable = [
        'listing_number',
        'fpo_organization_id',
        'crop_slug',
        'variety',
        'title',
        'total_aggregated_quantity_kg',
        'available_quantity_kg',
        'reserved_quantity_kg',
        'sold_quantity_kg',
        'asking_price_per_kg',
        'approved_grade',
        'district',
        'taluka',
        'pincode',
        'harvest_period',
        'description',
        'status',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'total_aggregated_quantity_kg' => 'decimal:2',
            'available_quantity_kg' => 'decimal:2',
            'reserved_quantity_kg' => 'decimal:2',
            'sold_quantity_kg' => 'decimal:2',
            'asking_price_per_kg' => 'decimal:2',
            'published_at' => 'datetime',
        ];
    }

    public function fpoOrganization(): BelongsTo
    {
        return $this->belongsTo(FpoOrganization::class, 'fpo_organization_id');
    }

    public function crop(): BelongsTo
    {
        return $this->belongsTo(Crop::class, 'crop_slug', 'slug');
    }

    public function memberAggregations(): HasMany
    {
        return $this->hasMany(FpoMemberAggregation::class, 'fpo_collective_listing_id');
    }

    public function rfqs(): HasMany
    {
        return $this->hasMany(Rfq::class, 'fpo_collective_listing_id');
    }

    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class, 'fpo_collective_listing_id');
    }

    public function isAvailable(): bool
    {
        return in_array($this->status, [self::STATUS_PUBLISHED, self::STATUS_PARTIALLY_SOLD])
            && $this->available_quantity_kg > 0;
    }

    public function getStatusBadgeClass(): string
    {
        return match ($this->status) {
            self::STATUS_DRAFT => 'bg-stone-100 text-stone-700 border-stone-300',
            self::STATUS_PUBLISHED => 'bg-emerald-100 text-emerald-800 border-emerald-300 font-bold',
            self::STATUS_PARTIALLY_SOLD => 'bg-amber-100 text-amber-800 border-amber-300',
            self::STATUS_SOLD_OUT => 'bg-purple-100 text-purple-800 border-purple-300',
            self::STATUS_WITHDRAWN => 'bg-stone-200 text-stone-600 border-stone-300',
            default => 'bg-stone-100 text-stone-800 border-stone-300',
        };
    }
}
