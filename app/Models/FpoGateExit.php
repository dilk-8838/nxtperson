<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FpoGateExit extends Model
{
    use HasFactory;

    public const MOVEMENT_ORDER_DISPATCH = 'order_dispatch';

    public const MOVEMENT_TRANSFER = 'transfer';

    public const MOVEMENT_RETURN_REJECTED = 'return_rejected';

    public const MOVEMENT_EMPTY_EXIT = 'empty_exit';

    public const STATUS_AUTHORIZED = 'authorized';

    public const STATUS_EXITED = 'exited';

    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'exit_number',
        'fpo_organization_id',
        'fpo_gate_entry_id',
        'purchase_order_id',
        'movement_type',
        'vehicle_number',
        'driver_name',
        'driver_phone',
        'destination',
        'dispatched_quantity',
        'unit',
        'exit_date_time',
        'authorized_by',
        'authorizer_name',
        'recorded_by',
        'status',
        'actual_exit_at',
        'handover_confirmed_at',
        'handover_confirmed_by',
        'handover_notes',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'dispatched_quantity' => 'decimal:2',
            'exit_date_time' => 'datetime',
            'actual_exit_at' => 'datetime',
            'handover_confirmed_at' => 'datetime',
        ];
    }

    public function fpoOrganization(): BelongsTo
    {
        return $this->belongsTo(FpoOrganization::class, 'fpo_organization_id');
    }

    public function gateEntry(): BelongsTo
    {
        return $this->belongsTo(FpoGateEntry::class, 'fpo_gate_entry_id');
    }

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id');
    }

    public function authorizer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'authorized_by');
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function handoverConfirmer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handover_confirmed_by');
    }

    public function getStatusBadgeClass(): string
    {
        return match ($this->status) {
            self::STATUS_AUTHORIZED => 'bg-amber-100 text-amber-800 border-amber-300 font-bold',
            self::STATUS_EXITED => 'bg-emerald-100 text-emerald-800 border-emerald-300 font-bold',
            self::STATUS_CANCELLED => 'bg-rose-100 text-rose-800 border-rose-300',
            default => 'bg-gray-100 text-gray-800 border-gray-300',
        };
    }
}
