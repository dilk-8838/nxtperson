<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FpoQualityApproval extends Model
{
    use HasFactory;

    public const DECISION_APPROVED = 'approved';

    public const DECISION_REJECTED = 'rejected';

    public const DECISION_PARTIALLY_ACCEPTED = 'partially_accepted';

    public const DECISION_QUARANTINED = 'quarantined';

    protected $fillable = [
        'approval_number',
        'fpo_gate_entry_id',
        'fpo_gate_entry_item_id',
        'approver_id',
        'approver_name',
        'approver_role',
        'decision',
        'decision_at',
        'accepted_quantity',
        'rejected_quantity',
        'quarantined_quantity',
        'assigned_grade',
        'notes',
        'rejection_reason',
        'signature_attestation',
        'signature_image_path',
    ];

    protected function casts(): array
    {
        return [
            'decision_at' => 'datetime',
            'accepted_quantity' => 'decimal:2',
            'rejected_quantity' => 'decimal:2',
            'quarantined_quantity' => 'decimal:2',
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

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    public function getDecisionBadgeClass(): string
    {
        return match ($this->decision) {
            self::DECISION_APPROVED => 'bg-emerald-100 text-emerald-800 border-emerald-300 font-bold',
            self::DECISION_REJECTED => 'bg-rose-100 text-rose-800 border-rose-300 font-bold',
            self::DECISION_PARTIALLY_ACCEPTED => 'bg-teal-100 text-teal-800 border-teal-300 font-bold',
            self::DECISION_QUARANTINED => 'bg-purple-100 text-purple-900 border-purple-300 font-bold',
            default => 'bg-gray-100 text-gray-800 border-gray-300',
        };
    }
}
