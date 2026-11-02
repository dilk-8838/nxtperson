<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FpoMemberAggregation extends Model
{
    use HasFactory;

    public const STATUS_COMMITTED = 'committed';

    public const STATUS_POOLED = 'pooled';

    public const STATUS_SOLD = 'sold';

    public const STATUS_RELEASED = 'released';

    protected $fillable = [
        'fpo_organization_id',
        'fpo_collective_listing_id',
        'farmer_user_id',
        'produce_lot_id',
        'committed_quantity_kg',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'committed_quantity_kg' => 'decimal:2',
        ];
    }

    public function fpoOrganization(): BelongsTo
    {
        return $this->belongsTo(FpoOrganization::class, 'fpo_organization_id');
    }

    public function collectiveListing(): BelongsTo
    {
        return $this->belongsTo(FpoCollectiveListing::class, 'fpo_collective_listing_id');
    }

    public function farmer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'farmer_user_id');
    }

    public function produceLot(): BelongsTo
    {
        return $this->belongsTo(ProduceLot::class, 'produce_lot_id');
    }
}
