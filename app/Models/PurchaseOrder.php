<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseOrder extends Model
{
    use HasFactory;

    public const STATUS_PENDING_CONFIRMATION = 'pending_confirmation';

    public const STATUS_CONFIRMED = 'confirmed';

    public const STATUS_PROCESSING = 'processing';

    public const STATUS_READY_FOR_DISPATCH = 'ready_for_dispatch';

    public const STATUS_DISPATCHED = 'dispatched';

    public const STATUS_DELIVERED = 'delivered';

    public const STATUS_CANCELLED = 'cancelled';

    public const PAYMENT_UNPAID = 'unpaid';

    public const PAYMENT_PENDING = 'payment_pending';

    public const PAYMENT_ESCROW_HELD = 'escrow_held';

    public const PAYMENT_PAID_OFFLINE = 'paid_offline';

    public const PAYMENT_REFUNDED = 'refunded';

    protected $fillable = [
        'po_number',
        'quotation_id',
        'rfq_id',
        'produce_reservation_id',
        'buyer_id',
        'seller_user_id',
        'seller_type',
        'supplier_organization_id',
        'fpo_organization_id',
        'item_type',
        'produce_lot_id',
        'product_id',
        'fpo_collective_listing_id',
        'item_description',
        'quantity',
        'unit',
        'unit_price',
        'subtotal',
        'additional_charges',
        'tax_amount',
        'total_amount',
        'delivery_address',
        'delivery_district',
        'estimated_delivery_date',
        'actual_delivery_date',
        'status',
        'payment_status',
        'payment_terms',
        'cancellation_reason',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'unit_price' => 'decimal:2',
            'subtotal' => 'decimal:2',
            'additional_charges' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'estimated_delivery_date' => 'date',
            'actual_delivery_date' => 'date',
        ];
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function sellerUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_user_id');
    }

    public function quotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class, 'quotation_id');
    }

    public function rfq(): BelongsTo
    {
        return $this->belongsTo(Rfq::class, 'rfq_id');
    }

    public function produceReservation(): BelongsTo
    {
        return $this->belongsTo(ProduceReservation::class, 'produce_reservation_id');
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

    public function statusLogs(): HasMany
    {
        return $this->hasMany(PurchaseOrderStatusLog::class, 'purchase_order_id')->orderBy('created_at', 'asc');
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

    public function getOrderNumberAttribute(): string
    {
        return $this->po_number;
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING_CONFIRMATION, self::STATUS_CONFIRMED]);
    }

    public function getStatusBadgeClass(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING_CONFIRMATION => 'bg-amber-100 text-amber-800 border-amber-300 font-bold',
            self::STATUS_CONFIRMED => 'bg-blue-100 text-blue-800 border-blue-300 font-bold',
            self::STATUS_PROCESSING => 'bg-indigo-100 text-indigo-800 border-indigo-300 font-bold',
            self::STATUS_READY_FOR_DISPATCH => 'bg-teal-100 text-teal-800 border-teal-300 font-bold',
            self::STATUS_DISPATCHED => 'bg-cyan-100 text-cyan-800 border-cyan-300 font-bold',
            self::STATUS_DELIVERED => 'bg-emerald-100 text-emerald-800 border-emerald-300 font-bold',
            self::STATUS_CANCELLED => 'bg-red-100 text-red-800 border-red-300',
            default => 'bg-slate-100 text-slate-800 border-slate-300',
        };
    }

    public function getStatusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING_CONFIRMATION => 'Pending Confirmation',
            self::STATUS_CONFIRMED => 'Order Confirmed',
            self::STATUS_PROCESSING => 'Processing & Packaging',
            self::STATUS_READY_FOR_DISPATCH => 'Ready for Dispatch',
            self::STATUS_DISPATCHED => 'In Transit / Dispatched',
            self::STATUS_DELIVERED => 'Delivered & Completed',
            self::STATUS_CANCELLED => 'Cancelled',
            default => ucfirst(str_replace('_', ' ', $this->status)),
        };
    }

    public function getPaymentStatusBadgeClass(): string
    {
        return match ($this->payment_status) {
            self::PAYMENT_UNPAID => 'bg-red-50 text-red-700 border-red-200',
            self::PAYMENT_PENDING => 'bg-amber-50 text-amber-700 border-amber-200',
            self::PAYMENT_ESCROW_HELD => 'bg-blue-50 text-blue-700 border-blue-200 font-semibold',
            self::PAYMENT_PAID_OFFLINE => 'bg-emerald-100 text-emerald-800 border-emerald-300 font-bold',
            self::PAYMENT_REFUNDED => 'bg-purple-50 text-purple-700 border-purple-200',
            default => 'bg-slate-50 text-slate-700 border-slate-200',
        };
    }
}
