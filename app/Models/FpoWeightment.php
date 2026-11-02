<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FpoWeightment extends Model
{
    use HasFactory;

    public const UNIT_KG = 'kg';

    public const UNIT_QUINTAL = 'quintal';

    public const UNIT_TONNE = 'tonne';

    public const METHOD_MANUAL = 'manual';

    public const METHOD_AUTOMATED = 'automated_weighbridge';

    protected $fillable = [
        'weightment_number',
        'fpo_gate_entry_id',
        'fpo_gate_entry_item_id',
        'sequence_number',
        'gross_weight',
        'tare_weight',
        'net_weight',
        'unit',
        'weighbridge_reference',
        'operator_name',
        'measurement_method',
        'is_manual_entry',
        'discrepancy_flag',
        'discrepancy_percentage',
        'discrepancy_reason',
        'discrepancy_resolved',
        'discrepancy_resolved_by',
        'discrepancy_resolution_notes',
        'is_accepted',
        'reweighment_of_id',
        'notes',
        'created_by',
        'measured_at',
    ];

    public function getHasDiscrepancyAttribute(): bool
    {
        return (bool) $this->discrepancy_flag;
    }

    protected function casts(): array
    {
        return [
            'gross_weight' => 'decimal:2',
            'tare_weight' => 'decimal:2',
            'net_weight' => 'decimal:2',
            'discrepancy_percentage' => 'decimal:2',
            'is_manual_entry' => 'boolean',
            'discrepancy_flag' => 'boolean',
            'discrepancy_resolved' => 'boolean',
            'is_accepted' => 'boolean',
            'measured_at' => 'datetime',
        ];
    }

    public function gateEntry(): BelongsTo
    {
        return $this->belongsTo(FpoGateEntry::class, 'fpo_gate_entry_id');
    }

    public function gateEntryItem(): BelongsTo
    {
        return $this->belongsTo(FpoGateEntryItem::class, 'fpo_gate_entry_item_id');
    }

    public function originalWeightment(): BelongsTo
    {
        return $this->belongsTo(self::class, 'reweighment_of_id');
    }

    public function reweighments(): HasMany
    {
        return $this->hasMany(self::class, 'reweighment_of_id');
    }

    public function resolver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'discrepancy_resolved_by');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Convert given weight to kg based on unit.
     */
    public static function convertToKg(float $weight, string $unit): float
    {
        return match (strtolower(trim($unit))) {
            'quintal', 'qtl' => round($weight * 100, 2),
            'tonne', 'ton', 'mt' => round($weight * 1000, 2),
            default => round($weight, 2),
        };
    }

    /**
     * Convert net weight to kg.
     */
    public function getNetWeightInKg(): float
    {
        return self::convertToKg((float) $this->net_weight, $this->unit);
    }
}
