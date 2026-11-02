<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FpoFarmerMembership extends Model
{
    use HasFactory;

    protected $fillable = [
        'fpo_organization_id',
        'farmer_user_id',
        'member_code',
        'share_units',
        'status',
        'joined_at',
        'notes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'joined_at' => 'date',
            'share_units' => 'integer',
        ];
    }

    /**
     * Parent FPO organization.
     */
    public function fpoOrganization(): BelongsTo
    {
        return $this->belongsTo(FpoOrganization::class, 'fpo_organization_id');
    }

    /**
     * Farmer user.
     */
    public function farmerUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'farmer_user_id');
    }
}
