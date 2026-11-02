<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProcurementRequirement extends Model
{
    use HasFactory;

    public const STATUS_OPEN = 'open';

    public const STATUS_RFQ_CREATED = 'rfq_created';

    public const STATUS_FULFILLED = 'fulfilled';

    public const STATUS_CLOSED = 'closed';

    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'requirement_number',
        'buyer_id',
        'type',
        'title',
        'item_name',
        'crop_slug',
        'category_slug',
        'target_quantity',
        'unit',
        'target_price_per_unit',
        'delivery_district',
        'delivery_address',
        'desired_delivery_date',
        'quality_specifications',
        'notes',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'target_quantity' => 'decimal:2',
            'target_price_per_unit' => 'decimal:2',
            'desired_delivery_date' => 'date',
        ];
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function crop(): BelongsTo
    {
        return $this->belongsTo(Crop::class, 'crop_slug', 'slug');
    }

    public function rfqs(): HasMany
    {
        return $this->hasMany(Rfq::class, 'procurement_requirement_id');
    }

    public function getStatusBadgeClass(): string
    {
        return match ($this->status) {
            self::STATUS_OPEN => 'bg-emerald-100 text-emerald-800 border-emerald-300',
            self::STATUS_RFQ_CREATED => 'bg-blue-100 text-blue-800 border-blue-300',
            self::STATUS_FULFILLED => 'bg-purple-100 text-purple-800 border-purple-300',
            self::STATUS_CLOSED => 'bg-slate-100 text-slate-700 border-slate-300',
            self::STATUS_CANCELLED => 'bg-red-100 text-red-800 border-red-300',
            default => 'bg-slate-100 text-slate-800 border-slate-300',
        };
    }
}
