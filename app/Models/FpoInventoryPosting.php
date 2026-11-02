<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FpoInventoryPosting extends Model
{
    use HasFactory;

    public const STATUS_POSTED = 'posted';

    public const STATUS_REVERSED = 'reversed';

    protected $fillable = [
        'posting_number',
        'fpo_organization_id',
        'fpo_gate_entry_id',
        'fpo_gate_entry_item_id',
        'produce_lot_id',
        'fpo_collective_listing_id',
        'posted_quantity_kg',
        'source_stock_before_kg',
        'source_stock_after_kg',
        'status',
        'posted_by',
        'posted_at',
        'reversal_reason',
        'reversed_by',
        'reversed_at',
        'idempotency_key',
    ];

    protected function casts(): array
    {
        return [
            'posted_quantity_kg' => 'decimal:2',
            'source_stock_before_kg' => 'decimal:2',
            'source_stock_after_kg' => 'decimal:2',
            'posted_at' => 'datetime',
            'reversed_at' => 'datetime',
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

    public function gateEntryItem(): BelongsTo
    {
        return $this->belongsTo(FpoGateEntryItem::class, 'fpo_gate_entry_item_id');
    }

    public function produceLot(): BelongsTo
    {
        return $this->belongsTo(ProduceLot::class, 'produce_lot_id');
    }

    public function collectiveListing(): BelongsTo
    {
        return $this->belongsTo(FpoCollectiveListing::class, 'fpo_collective_listing_id');
    }

    public function poster(): BelongsTo
    {
        return $this->belongsTo(User::class, 'posted_by');
    }

    public function reverser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reversed_by');
    }
}
