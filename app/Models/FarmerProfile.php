<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FarmerProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'farm_name',
        'village',
        'taluka',
        'district',
        'state',
        'pincode',
        'total_acreage',
        'cultivated_acreage',
        'soil_type',
        'irrigation_type',
        'crops_cultivated',
        'expected_harvest_date',
        'estimated_harvest_quantity',
        'estimated_harvest_unit',
        'kisan_credit_card',
        'survey_number',
        'verification_status',
        'verified_at',
        'verified_by',
        'rejection_reason',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'crops_cultivated' => 'array',
            'expected_harvest_date' => 'date',
            'verified_at' => 'datetime',
            'total_acreage' => 'float',
            'cultivated_acreage' => 'float',
            'estimated_harvest_quantity' => 'float',
        ];
    }

    /**
     * Owning user account.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Verifying administrator.
     */
    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Check if profile is verified.
     */
    public function isVerified(): bool
    {
        return $this->verification_status === 'verified';
    }

    /**
     * Calculate profile completeness percentage (0 to 100).
     */
    public function completionPercentage(): int
    {
        $fields = [
            'farm_name',
            'village',
            'district',
            'total_acreage',
            'cultivated_acreage',
            'soil_type',
            'irrigation_type',
            'crops_cultivated',
            'expected_harvest_date',
            'estimated_harvest_quantity',
        ];

        $completed = 0;
        foreach ($fields as $field) {
            $val = $this->getAttribute($field);
            if (! empty($val)) {
                $completed++;
            }
        }

        return (int) round(($completed / count($fields)) * 100);
    }
}
