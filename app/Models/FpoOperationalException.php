<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FpoOperationalException extends Model
{
    use HasFactory;

    public const TYPE_WEIGHT_DISCREPANCY = 'weight_discrepancy';

    public const TYPE_QUALITY_FAILURE = 'quality_failure';

    public const TYPE_MISSING_SAMPLE = 'missing_sample';

    public const TYPE_STOCK_MISMATCH = 'stock_mismatch';

    public const TYPE_QUANTITY_MISMATCH = 'quantity_mismatch';

    public const TYPE_UNAUTHORIZED_EXIT = 'unauthorized_exit';

    public const SEVERITY_LOW = 'low';

    public const SEVERITY_MEDIUM = 'medium';

    public const SEVERITY_CRITICAL = 'critical';

    public const STATUS_OPEN = 'open';

    public const STATUS_UNDER_REVIEW = 'under_review';

    public const STATUS_RESOLVED = 'resolved';

    public const STATUS_CLOSED = 'closed';

    public const STATUS_REOPENED = 'reopened';

    protected $fillable = [
        'exception_number',
        'fpo_organization_id',
        'fpo_gate_entry_id',
        'fpo_gate_entry_item_id',
        'exception_type',
        'severity',
        'title',
        'description',
        'status',
        'created_by',
        'assigned_to',
        'resolution_action',
        'resolution_reason',
        'resolved_by',
        'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'resolved_at' => 'datetime',
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

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function resolver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    public function getSeverityBadgeClass(): string
    {
        return match ($this->severity) {
            self::SEVERITY_CRITICAL => 'bg-rose-100 text-rose-800 border-rose-300 font-bold',
            self::SEVERITY_MEDIUM => 'bg-amber-100 text-amber-800 border-amber-300 font-semibold',
            self::SEVERITY_LOW => 'bg-blue-100 text-blue-800 border-blue-300',
            default => 'bg-gray-100 text-gray-800 border-gray-300',
        };
    }

    public function getStatusBadgeClass(): string
    {
        return match ($this->status) {
            self::STATUS_OPEN => 'bg-rose-100 text-rose-800 border-rose-300 font-bold',
            self::STATUS_UNDER_REVIEW => 'bg-amber-100 text-amber-800 border-amber-300 font-medium',
            self::STATUS_RESOLVED, self::STATUS_CLOSED => 'bg-emerald-100 text-emerald-800 border-emerald-300 font-bold',
            self::STATUS_REOPENED => 'bg-purple-100 text-purple-900 border-purple-300 font-bold',
            default => 'bg-gray-100 text-gray-800 border-gray-300',
        };
    }
}
