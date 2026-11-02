<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FpoOrganization extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REJECTED = 'rejected';

    public const STATUS_SUSPENDED = 'suspended';

    protected $fillable = [
        'name',
        'slug',
        'organization_type',
        'registration_number',
        'incorporation_year',
        'description',
        'registered_address',
        'village',
        'taluka',
        'district',
        'state',
        'pincode',
        'authorized_representative_name',
        'contact_email',
        'contact_phone',
        'bank_name',
        'bank_account_number',
        'ifsc_code',
        'document_path',
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
            'incorporation_year' => 'integer',
            'verified_at' => 'datetime',
        ];
    }

    /**
     * Authorized staff / directors pivot records.
     */
    public function fpoMembers(): HasMany
    {
        return $this->hasMany(FpoMember::class, 'fpo_organization_id');
    }

    /**
     * Users authorized to administer or represent this FPO.
     */
    public function authorizedUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'fpo_members')
            ->withPivot(['role_in_fpo', 'is_primary'])
            ->withTimestamps();
    }

    public function primaryContact(): ?User
    {
        return $this->authorizedUsers()->wherePivot('is_primary', true)->first()
            ?? $this->authorizedUsers()->first();
    }

    /**
     * Farmer membership linkages.
     */
    public function farmerMemberships(): HasMany
    {
        return $this->hasMany(FpoFarmerMembership::class, 'fpo_organization_id');
    }

    /**
     * Farmer users belonging to this FPO.
     */
    public function memberFarmers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'fpo_farmer_memberships', 'fpo_organization_id', 'farmer_user_id')
            ->withPivot(['member_code', 'share_units', 'status', 'joined_at', 'notes'])
            ->withTimestamps();
    }

    public function gateEntries(): HasMany
    {
        return $this->hasMany(FpoGateEntry::class, 'fpo_organization_id');
    }

    public function gateExits(): HasMany
    {
        return $this->hasMany(FpoGateExit::class, 'fpo_organization_id');
    }

    public function inventoryPostings(): HasMany
    {
        return $this->hasMany(FpoInventoryPosting::class, 'fpo_organization_id');
    }

    public function operationalExceptions(): HasMany
    {
        return $this->hasMany(FpoOperationalException::class, 'fpo_organization_id');
    }

    /**
     * Admin who verified this FPO.
     */
    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Check if FPO is verified & approved.
     */
    public function isApproved(): bool
    {
        return $this->verification_status === self::STATUS_APPROVED;
    }

    /**
     * Check if FPO status is pending review.
     */
    public function isPending(): bool
    {
        return $this->verification_status === self::STATUS_PENDING;
    }

    /**
     * Check if user is an authorized staff/representative for this FPO.
     */
    public function hasStaffMember(User $user): bool
    {
        return $this->fpoMembers()->where('user_id', $user->id)->exists();
    }

    /**
     * Calculate total land acreage aggregated across active member farmers.
     */
    public function totalMemberAcreage(): float
    {
        $farmerIds = $this->farmerMemberships()
            ->where('status', 'active')
            ->pluck('farmer_user_id');

        return (float) FarmerProfile::whereIn('user_id', $farmerIds)->sum('total_acreage');
    }

    /**
     * Get distinct crops cultivated across active member farmers.
     */
    public function distinctMemberCrops(): array
    {
        $farmerIds = $this->farmerMemberships()
            ->where('status', 'active')
            ->pluck('farmer_user_id');

        $profiles = FarmerProfile::whereIn('user_id', $farmerIds)->get(['crops_cultivated']);
        $crops = [];
        foreach ($profiles as $profile) {
            if (is_array($profile->crops_cultivated)) {
                foreach ($profile->crops_cultivated as $crop) {
                    $crops[strtolower(trim($crop))] = true;
                }
            }
        }

        return array_keys($crops);
    }

    public function collectiveListings(): HasMany
    {
        return $this->hasMany(FpoCollectiveListing::class, 'fpo_organization_id');
    }

    public function memberAggregations(): HasMany
    {
        return $this->hasMany(FpoMemberAggregation::class, 'fpo_organization_id');
    }

    public function quotations(): HasMany
    {
        return $this->hasMany(Quotation::class, 'fpo_organization_id');
    }

    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class, 'fpo_organization_id');
    }

    public function rfqInvitations(): HasMany
    {
        return $this->hasMany(RfqInvitation::class, 'fpo_organization_id');
    }
}
