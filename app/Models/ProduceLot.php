<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ProduceLot extends Model
{
    use HasFactory;

    public const STATUS_DRAFT = 'draft';

    public const STATUS_SUBMITTED = 'submitted';

    public const STATUS_ASSESSED = 'assessed';

    public const STATUS_UNDER_REVIEW = 'under_review';

    public const STATUS_APPROVED_FOR_PROCUREMENT = 'approved_for_procurement';

    public const STATUS_PURCHASE_CONFIRMED = 'purchase_confirmed';

    public const STATUS_INVENTORY_AVAILABLE = 'inventory_available';

    public const STATUS_PARTIALLY_SOLD = 'partially_sold';

    public const STATUS_SOLD_OUT = 'sold_out';

    public const STATUS_REJECTED = 'rejected';

    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'lot_number',
        'farmer_id',
        'crop_slug',
        'variety',
        'quantity',
        'unit',
        'quantity_in_kg',
        'district',
        'taluka',
        'pincode',
        'harvest_date',
        'expected_price_per_kg',
        'description',
        'farming_method',
        'status',
        'rejection_reason',
        'admin_notes',
        'approved_grade',
        'approved_procurement_price_per_kg',
        'buyer_sale_price_per_kg',
        'confirmed_quantity_kg',
        'available_quantity_kg',
        'reserved_quantity_kg',
        'sold_quantity_kg',
        'aggregated_quantity_kg',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'harvest_date' => 'date',
            'quantity' => 'decimal:2',
            'quantity_in_kg' => 'decimal:2',
            'expected_price_per_kg' => 'decimal:2',
            'approved_procurement_price_per_kg' => 'decimal:2',
            'buyer_sale_price_per_kg' => 'decimal:2',
            'confirmed_quantity_kg' => 'decimal:2',
            'available_quantity_kg' => 'decimal:2',
            'reserved_quantity_kg' => 'decimal:2',
            'sold_quantity_kg' => 'decimal:2',
            'aggregated_quantity_kg' => 'decimal:2',
            'published_at' => 'datetime',
        ];
    }

    /**
     * Relationship: Farmer who harvested the lot.
     */
    public function farmer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'farmer_id');
    }

    /**
     * Relationship: Crop definition.
     */
    public function crop(): BelongsTo
    {
        return $this->belongsTo(Crop::class, 'crop_slug', 'slug');
    }

    /**
     * Relationship: Multiple uploaded photographs.
     */
    public function images(): HasMany
    {
        return $this->hasMany(ProduceLotImage::class, 'produce_lot_id');
    }

    /**
     * Primary display image.
     */
    public function primaryImage(): HasOne
    {
        return $this->hasOne(ProduceLotImage::class, 'produce_lot_id')
            ->ofMany(['is_primary' => 'max', 'id' => 'min']);
    }

    /**
     * Relationship: Quality Grading Assessments.
     */
    public function assessments(): HasMany
    {
        return $this->hasMany(ProduceGradingAssessment::class, 'produce_lot_id')->orderBy('id', 'desc');
    }

    /**
     * Latest quality assessment.
     */
    public function latestAssessment(): HasOne
    {
        return $this->hasOne(ProduceGradingAssessment::class, 'produce_lot_id')->latestOfMany();
    }

    /**
     * Relationship: Buyer reservations.
     */
    public function reservations(): HasMany
    {
        return $this->hasMany(ProduceReservation::class, 'produce_lot_id')->orderBy('id', 'desc');
    }

    public function memberAggregations(): HasMany
    {
        return $this->hasMany(FpoMemberAggregation::class, 'produce_lot_id');
    }

    public function rfqs(): HasMany
    {
        return $this->hasMany(Rfq::class, 'produce_lot_id');
    }

    public function quotations(): HasMany
    {
        return $this->hasMany(Quotation::class, 'produce_lot_id');
    }

    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class, 'produce_lot_id');
    }

    /**
     * Determine if stock is currently purchasable/reservable by buyers.
     */
    public function isAvailableForBuyers(): bool
    {
        return in_array($this->status, [self::STATUS_INVENTORY_AVAILABLE, self::STATUS_PARTIALLY_SOLD])
            && $this->available_quantity_kg > 0;
    }

    /**
     * Status UI badge styling.
     */
    public function getStatusBadgeClass(): string
    {
        return match ($this->status) {
            self::STATUS_DRAFT => 'bg-stone-100 text-stone-700 border-stone-300',
            self::STATUS_SUBMITTED => 'bg-amber-50 text-amber-800 border-amber-300',
            self::STATUS_ASSESSED => 'bg-blue-50 text-blue-800 border-blue-300',
            self::STATUS_UNDER_REVIEW => 'bg-indigo-50 text-indigo-800 border-indigo-300',
            self::STATUS_APPROVED_FOR_PROCUREMENT => 'bg-teal-50 text-teal-800 border-teal-300',
            self::STATUS_PURCHASE_CONFIRMED => 'bg-cyan-50 text-cyan-800 border-cyan-300',
            self::STATUS_INVENTORY_AVAILABLE => 'bg-emerald-100 text-emerald-800 border-emerald-400 font-bold',
            self::STATUS_PARTIALLY_SOLD => 'bg-emerald-50 text-emerald-700 border-emerald-300',
            self::STATUS_SOLD_OUT => 'bg-purple-100 text-purple-800 border-purple-300',
            self::STATUS_REJECTED => 'bg-red-100 text-red-800 border-red-300',
            self::STATUS_CANCELLED => 'bg-stone-200 text-stone-600 border-stone-300',
            default => 'bg-stone-100 text-stone-800 border-stone-300',
        };
    }

    /**
     * Status human-readable label.
     */
    public function getStatusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_SUBMITTED => 'Submitted for Grading',
            self::STATUS_ASSESSED => 'Grading Assessed',
            self::STATUS_UNDER_REVIEW => 'Under Review',
            self::STATUS_APPROVED_FOR_PROCUREMENT => 'Approved for Procurement',
            self::STATUS_PURCHASE_CONFIRMED => 'Purchase Confirmed',
            self::STATUS_INVENTORY_AVAILABLE => 'Inventory Available (Published)',
            self::STATUS_PARTIALLY_SOLD => 'Partially Reserved / Sold',
            self::STATUS_SOLD_OUT => 'Sold Out',
            self::STATUS_REJECTED => 'Rejected',
            self::STATUS_CANCELLED => 'Cancelled',
            default => ucfirst(str_replace('_', ' ', $this->status)),
        };
    }

    /**
     * Quality Grade UI badge styling.
     */
    public function getGradeBadgeClass(): string
    {
        return match ($this->approved_grade) {
            'Good' => 'bg-emerald-500 text-white border-emerald-600',
            'Intermediate' => 'bg-amber-500 text-white border-amber-600',
            'Bad' => 'bg-red-500 text-white border-red-600',
            default => 'bg-stone-300 text-stone-800 border-stone-400',
        };
    }
}
