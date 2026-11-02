<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class FpoGateEntryItem extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';

    public const STATUS_WEIGHED = 'weighed';

    public const STATUS_TESTED = 'tested';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REJECTED = 'rejected';

    public const STATUS_PARTIALLY_ACCEPTED = 'partially_accepted';

    public const STATUS_INVENTORY_POSTED = 'inventory_posted';

    public const STATUS_QUARANTINED = 'quarantined';

    protected $fillable = [
        'fpo_gate_entry_id',
        'item_number',
        'produce_lot_id',
        'crop_name',
        'variety',
        'declared_quantity',
        'declared_unit',
        'measured_net_quantity',
        'accepted_quantity',
        'rejected_quantity',
        'quarantined_quantity',
        'assigned_grade',
        'status',
        'remarks',
    ];

    protected function casts(): array
    {
        return [
            'declared_quantity' => 'decimal:2',
            'measured_net_quantity' => 'decimal:2',
            'accepted_quantity' => 'decimal:2',
            'rejected_quantity' => 'decimal:2',
            'quarantined_quantity' => 'decimal:2',
        ];
    }

    public function gateEntry(): BelongsTo
    {
        return $this->belongsTo(FpoGateEntry::class, 'fpo_gate_entry_id');
    }

    public function produceLot(): BelongsTo
    {
        return $this->belongsTo(ProduceLot::class, 'produce_lot_id');
    }

    public function weightments(): HasMany
    {
        return $this->hasMany(FpoWeightment::class, 'fpo_gate_entry_item_id');
    }

    public function samples(): HasMany
    {
        return $this->hasMany(FpoSample::class, 'fpo_gate_entry_item_id');
    }

    public function qualityTests(): HasMany
    {
        return $this->hasMany(FpoQualityTest::class, 'fpo_gate_entry_item_id');
    }

    public function approvals(): HasMany
    {
        return $this->hasMany(FpoQualityApproval::class, 'fpo_gate_entry_item_id')->orderBy('id', 'desc');
    }

    public function latestApproval(): HasOne
    {
        return $this->hasOne(FpoQualityApproval::class, 'fpo_gate_entry_item_id')->latestOfMany();
    }

    public function inventoryPostings(): HasMany
    {
        return $this->hasMany(FpoInventoryPosting::class, 'fpo_gate_entry_item_id');
    }
}
