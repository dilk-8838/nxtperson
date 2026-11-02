<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class FpoGateEntry extends Model
{
    use HasFactory;

    public const STATUS_REGISTERED = 'registered';

    public const STATUS_ENTERED = 'entered';

    public const STATUS_WEIGHED = 'weighed';

    public const STATUS_SAMPLING_PENDING = 'sampling_pending';

    public const STATUS_TESTING_IN_PROGRESS = 'testing_in_progress';

    public const STATUS_QUALITY_PENDING_APPROVAL = 'quality_pending_approval';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REJECTED = 'rejected';

    public const STATUS_PARTIALLY_ACCEPTED = 'partially_accepted';

    public const STATUS_INVENTORY_POSTED = 'inventory_posted';

    public const STATUS_EXIT_AUTHORIZED = 'exit_authorized';

    public const STATUS_EXITED = 'exited';

    public const STATUS_CANCELLED = 'cancelled';

    public const STATUS_QUARANTINED = 'quarantined';

    public const STATUS_ON_HOLD = 'on_hold';

    public const ENTRY_TYPE_VEHICLE = 'vehicle_delivery';

    public const ENTRY_TYPE_WALKIN = 'walk_in';

    public const ENTRY_TYPE_TRANSFER = 'transfer';

    public const ENTRY_TYPE_RETURN = 'return';

    protected $fillable = [
        'entry_number',
        'fpo_organization_id',
        'entry_date_time',
        'gate_point',
        'entry_type',
        'vehicle_number',
        'driver_name',
        'driver_phone',
        'delivering_party_type',
        'farmer_user_id',
        'delivering_party_name',
        'delivering_party_contact',
        'purchase_order_id',
        'source_produce_lot_id',
        'fpo_collective_listing_id',
        'crop_name',
        'variety',
        'declared_quantity',
        'declared_unit',
        'initial_condition',
        'status',
        'created_by',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'entry_date_time' => 'datetime',
            'declared_quantity' => 'decimal:2',
        ];
    }

    public function fpoOrganization(): BelongsTo
    {
        return $this->belongsTo(FpoOrganization::class, 'fpo_organization_id');
    }

    public function farmer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'farmer_user_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id');
    }

    public function sourceProduceLot(): BelongsTo
    {
        return $this->belongsTo(ProduceLot::class, 'source_produce_lot_id');
    }

    public function collectiveListing(): BelongsTo
    {
        return $this->belongsTo(FpoCollectiveListing::class, 'fpo_collective_listing_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(FpoGateEntryItem::class, 'fpo_gate_entry_id');
    }

    public function weightments(): HasMany
    {
        return $this->hasMany(FpoWeightment::class, 'fpo_gate_entry_id')->orderBy('sequence_number', 'asc');
    }

    public function acceptedWeightment(): HasOne
    {
        return $this->hasOne(FpoWeightment::class, 'fpo_gate_entry_id')
            ->where('is_accepted', true)
            ->latestOfMany();
    }

    public function samples(): HasMany
    {
        return $this->hasMany(FpoSample::class, 'fpo_gate_entry_id');
    }

    public function approvals(): HasMany
    {
        return $this->hasMany(FpoQualityApproval::class, 'fpo_gate_entry_id')->orderBy('id', 'desc');
    }

    public function latestApproval(): HasOne
    {
        return $this->hasOne(FpoQualityApproval::class, 'fpo_gate_entry_id')->latestOfMany();
    }

    public function inventoryPostings(): HasMany
    {
        return $this->hasMany(FpoInventoryPosting::class, 'fpo_gate_entry_id');
    }

    public function gateExits(): HasMany
    {
        return $this->hasMany(FpoGateExit::class, 'fpo_gate_entry_id');
    }

    public function latestExit(): HasOne
    {
        return $this->hasOne(FpoGateExit::class, 'fpo_gate_entry_id')->latestOfMany();
    }

    public function exceptions(): HasMany
    {
        return $this->hasMany(FpoOperationalException::class, 'fpo_gate_entry_id');
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(FpoOperationAuditLog::class, 'fpo_gate_entry_id')->orderBy('created_at', 'asc');
    }

    public function isVehicleDelivery(): bool
    {
        return $this->entry_type === self::ENTRY_TYPE_VEHICLE;
    }

    public function hasOpenExceptions(): bool
    {
        return $this->exceptions()->whereIn('status', ['open', 'under_review', 'reopened'])->exists();
    }

    public function getStatusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_REGISTERED => 'Gate Registered',
            self::STATUS_ENTERED => 'Vehicle Entered',
            self::STATUS_WEIGHED => 'Weighed & Reconciled',
            self::STATUS_SAMPLING_PENDING => 'Awaiting Sample',
            self::STATUS_TESTING_IN_PROGRESS => 'Testing in Progress',
            self::STATUS_QUALITY_PENDING_APPROVAL => 'Pending Quality Approval',
            self::STATUS_APPROVED => 'Quality Approved',
            self::STATUS_REJECTED => 'Quality Rejected',
            self::STATUS_PARTIALLY_ACCEPTED => 'Partially Accepted',
            self::STATUS_INVENTORY_POSTED => 'Inventory Posted',
            self::STATUS_EXIT_AUTHORIZED => 'Exit Authorized',
            self::STATUS_EXITED => 'Gate Exited',
            self::STATUS_CANCELLED => 'Entry Cancelled',
            self::STATUS_QUARANTINED => 'Quarantined',
            self::STATUS_ON_HOLD => 'On Hold',
            default => ucfirst(str_replace('_', ' ', $this->status)),
        };
    }

    public function getStatusBadgeClass(): string
    {
        return match ($this->status) {
            self::STATUS_REGISTERED => 'bg-slate-100 text-slate-800 border-slate-300',
            self::STATUS_ENTERED => 'bg-blue-50 text-blue-700 border-blue-200',
            self::STATUS_WEIGHED => 'bg-indigo-50 text-indigo-700 border-indigo-200',
            self::STATUS_SAMPLING_PENDING, self::STATUS_TESTING_IN_PROGRESS => 'bg-amber-50 text-amber-800 border-amber-300 font-medium',
            self::STATUS_QUALITY_PENDING_APPROVAL => 'bg-yellow-100 text-yellow-900 border-yellow-300 font-bold',
            self::STATUS_APPROVED => 'bg-emerald-100 text-emerald-800 border-emerald-300 font-bold',
            self::STATUS_PARTIALLY_ACCEPTED => 'bg-teal-50 text-teal-800 border-teal-300 font-semibold',
            self::STATUS_INVENTORY_POSTED => 'bg-emerald-600 text-white border-emerald-700 font-bold',
            self::STATUS_EXIT_AUTHORIZED => 'bg-cyan-100 text-cyan-800 border-cyan-300',
            self::STATUS_EXITED => 'bg-stone-200 text-stone-700 border-stone-300',
            self::STATUS_REJECTED, self::STATUS_CANCELLED => 'bg-rose-100 text-rose-800 border-rose-300 font-bold',
            self::STATUS_QUARANTINED, self::STATUS_ON_HOLD => 'bg-purple-100 text-purple-900 border-purple-300 font-bold',
            default => 'bg-gray-100 text-gray-800 border-gray-300',
        };
    }
}
