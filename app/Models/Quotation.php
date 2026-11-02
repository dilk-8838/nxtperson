<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Quotation extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';

    public const STATUS_ACCEPTED = 'accepted';

    public const STATUS_DECLINED = 'declined';

    public const STATUS_WITHDRAWN = 'withdrawn';

    public const STATUS_EXPIRED = 'expired';

    protected $fillable = [
        'quotation_number',
        'rfq_id',
        'seller_user_id',
        'seller_type',
        'supplier_organization_id',
        'fpo_organization_id',
        'produce_lot_id',
        'product_id',
        'fpo_collective_listing_id',
        'quoted_quantity',
        'unit',
        'unit_price',
        'additional_charges',
        'tax_amount',
        'total_amount',
        'estimated_delivery_date',
        'valid_until',
        'status',
        'quality_notes',
        'terms_and_conditions',
    ];

    protected function casts(): array
    {
        return [
            'quoted_quantity' => 'decimal:2',
            'unit_price' => 'decimal:2',
            'additional_charges' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'estimated_delivery_date' => 'date',
            'valid_until' => 'date',
        ];
    }

    public function rfq(): BelongsTo
    {
        return $this->belongsTo(Rfq::class, 'rfq_id');
    }

    public function sellerUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_user_id');
    }

    public function supplierOrganization(): BelongsTo
    {
        return $this->belongsTo(SupplierOrganization::class, 'supplier_organization_id');
    }

    public function fpoOrganization(): BelongsTo
    {
        return $this->belongsTo(FpoOrganization::class, 'fpo_organization_id');
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

    public function purchaseOrder(): HasOne
    {
        return $this->hasOne(PurchaseOrder::class, 'quotation_id');
    }

    public function getSellerDisplayName(): string
    {
        if ($this->seller_type === 'supplier' && $this->supplierOrganization) {
            return $this->supplierOrganization->name;
        }

        if ($this->seller_type === 'fpo' && $this->fpoOrganization) {
            return $this->fpoOrganization->name;
        }

        return $this->sellerUser?->name ?? 'Direct Producer';
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isAcceptable(): bool
    {
        return $this->status === self::STATUS_PENDING && $this->valid_until >= now()->toDateString();
    }

    public function getStatusBadgeClass(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'bg-amber-100 text-amber-800 border-amber-300 font-bold',
            self::STATUS_ACCEPTED => 'bg-emerald-100 text-emerald-800 border-emerald-300 font-bold',
            self::STATUS_DECLINED => 'bg-slate-100 text-slate-700 border-slate-300',
            self::STATUS_WITHDRAWN => 'bg-stone-100 text-stone-600 border-stone-300',
            self::STATUS_EXPIRED => 'bg-red-100 text-red-800 border-red-300',
            default => 'bg-slate-100 text-slate-800 border-slate-300',
        };
    }
}
