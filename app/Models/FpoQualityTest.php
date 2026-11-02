<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FpoQualityTest extends Model
{
    use HasFactory;

    public const OUTCOME_PASS = 'pass';

    public const OUTCOME_FAIL = 'fail';

    public const OUTCOME_CONDITIONAL = 'conditional';

    protected $fillable = [
        'test_number',
        'fpo_sample_id',
        'fpo_gate_entry_item_id',
        'test_type',
        'parameter_name',
        'measured_value',
        'unit',
        'min_acceptable_value',
        'max_acceptable_value',
        'outcome',
        'tested_by',
        'tester_name',
        'tested_at',
        'notes',
        'is_superseded',
        'superseded_by_id',
    ];

    protected function casts(): array
    {
        return [
            'measured_value' => 'decimal:2',
            'min_acceptable_value' => 'decimal:2',
            'max_acceptable_value' => 'decimal:2',
            'tested_at' => 'datetime',
            'is_superseded' => 'boolean',
        ];
    }

    public function sample(): BelongsTo
    {
        return $this->belongsTo(FpoSample::class, 'fpo_sample_id');
    }

    public function gateEntryItem(): BelongsTo
    {
        return $this->belongsTo(FpoGateEntryItem::class, 'fpo_gate_entry_item_id');
    }

    public function tester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tested_by');
    }

    public function supersededBy(): BelongsTo
    {
        return $this->belongsTo(self::class, 'superseded_by_id');
    }

    public function getOutcomeBadgeClass(): string
    {
        return match ($this->outcome) {
            self::OUTCOME_PASS => 'bg-emerald-100 text-emerald-800 border-emerald-300 font-bold',
            self::OUTCOME_FAIL => 'bg-rose-100 text-rose-800 border-rose-300 font-bold',
            self::OUTCOME_CONDITIONAL => 'bg-amber-100 text-amber-800 border-amber-300 font-medium',
            default => 'bg-gray-100 text-gray-800 border-gray-300',
        };
    }
}
