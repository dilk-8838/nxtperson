<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FpoSample extends Model
{
    use HasFactory;

    public const STATUS_COLLECTED = 'collected';

    public const STATUS_IN_TESTING = 'in_testing';

    public const STATUS_TESTED = 'tested';

    public const STATUS_INVALID = 'invalid';

    public const STATUS_RETEST_REQUESTED = 'retest_requested';

    protected $fillable = [
        'sample_number',
        'fpo_gate_entry_id',
        'fpo_gate_entry_item_id',
        'crop_name',
        'variety',
        'sample_date_time',
        'collected_by',
        'collector_name',
        'sampling_method',
        'sample_quantity',
        'sample_unit',
        'container_seal_number',
        'condition_observations',
        'destination',
        'status',
        'attachment_path',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'sample_date_time' => 'datetime',
            'sample_quantity' => 'decimal:2',
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

    public function collector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'collected_by');
    }

    public function qualityTests(): HasMany
    {
        return $this->hasMany(FpoQualityTest::class, 'fpo_sample_id');
    }
}
