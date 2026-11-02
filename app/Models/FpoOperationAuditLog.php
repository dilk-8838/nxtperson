<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FpoOperationAuditLog extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'fpo_organization_id',
        'fpo_gate_entry_id',
        'entity_type',
        'entity_id',
        'action',
        'from_state',
        'to_state',
        'actor_id',
        'actor_name',
        'actor_role',
        'reason',
        'metadata',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'created_at' => 'datetime',
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

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    /**
     * Helper to append an audit log entry.
     */
    public static function log(
        int $fpoOrgId,
        ?int $gateEntryId,
        string $entityType,
        int $entityId,
        string $action,
        User $actor,
        ?string $fromState = null,
        ?string $toState = null,
        ?string $reason = null,
        ?array $metadata = null
    ): self {
        return self::create([
            'fpo_organization_id' => $fpoOrgId,
            'fpo_gate_entry_id' => $gateEntryId,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'action' => $action,
            'from_state' => $fromState,
            'to_state' => $toState,
            'actor_id' => $actor->id,
            'actor_name' => $actor->name,
            'actor_role' => $actor->role,
            'reason' => $reason,
            'metadata' => $metadata,
            'created_at' => now(),
        ]);
    }
}
