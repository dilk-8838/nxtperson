<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rfq extends Model
{
    use HasFactory;

    public const STATUS_OPEN = 'open';

    public const STATUS_QUOTATIONS_RECEIVED = 'quotations_received';

    public const STATUS_PARTIALLY_ACCEPTED = 'partially_accepted';

    public const STATUS_ACCEPTED = 'accepted';

    public const STATUS_CLOSED = 'closed';

    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'rfq_number',
        'buyer_id',
        'procurement_requirement_id',
        'item_type',
        'produce_lot_id',
        'product_id',
        'fpo_collective_listing_id',
        'title',
        'quantity',
        'unit',
        'target_price_per_unit',
        'delivery_district',
        'delivery_address',
        'target_delivery_date',
        'specifications',
        'status',
        'closes_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'target_price_per_unit' => 'decimal:2',
            'target_delivery_date' => 'date',
            'closes_at' => 'datetime',
        ];
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function requirement(): BelongsTo
    {
        return $this->belongsTo(ProcurementRequirement::class, 'procurement_requirement_id');
    }

    public function produceLot(): BelongsTo
    {
        return $this->belongsTo(ProduceLot::class, 'produce_lot_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function collectiveListing(): BelongsTo
    {
        return $this->belongsTo(FpoCollectiveListing::class, 'fpo_collective_listing_id');
    }

    public function invitations(): HasMany
    {
        return $this->hasMany(RfqInvitation::class, 'rfq_id');
    }

    public function quotations(): HasMany
    {
        return $this->hasMany(Quotation::class, 'rfq_id')->orderBy('total_amount', 'asc');
    }

    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class, 'rfq_id');
    }

    public function isOpen(): bool
    {
        return in_array($this->status, [self::STATUS_OPEN, self::STATUS_QUOTATIONS_RECEIVED]);
    }

    public function getStatusBadgeClass(): string
    {
        return match ($this->status) {
            self::STATUS_OPEN => 'bg-emerald-100 text-emerald-800 border-emerald-300 font-bold',
            self::STATUS_QUOTATIONS_RECEIVED => 'bg-blue-100 text-blue-800 border-blue-300',
            self::STATUS_ACCEPTED => 'bg-purple-100 text-purple-800 border-purple-300 font-bold',
            self::STATUS_CLOSED => 'bg-slate-100 text-slate-700 border-slate-300',
            self::STATUS_CANCELLED => 'bg-red-100 text-red-800 border-red-300',
            default => 'bg-slate-100 text-slate-800 border-slate-300',
        };
    }
}
