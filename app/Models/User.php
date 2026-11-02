<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'role',
        'status',
        'rejection_reason',
        'district',
        'state',
        'pincode',
        'otp_code',
        'otp_expires_at',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'otp_code',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'otp_expires_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Check if user is a farmer.
     */
    public function isFarmer(): bool
    {
        return $this->role === 'farmer';
    }

    /**
     * Check if user is a supplier (or distributor).
     */
    public function isSupplier(): bool
    {
        return in_array($this->role, ['supplier', 'distributor']);
    }

    /**
     * Check if user is a distributor (backward compatibility).
     */
    public function isDistributor(): bool
    {
        return in_array($this->role, ['distributor', 'supplier']);
    }

    /**
     * Check if user is an administrator.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is a buyer.
     */
    public function isBuyer(): bool
    {
        return $this->role === 'buyer';
    }

    /**
     * Check if user is an FPO / Cooperative representative.
     */
    public function isFpo(): bool
    {
        return in_array($this->role, ['fpo', 'cooperative']);
    }

    /**
     * Check role matches.
     */
    public function hasRole(string $role): bool
    {
        if ($role === 'supplier' || $role === 'distributor') {
            return $this->isSupplier();
        }
        if ($role === 'fpo' || $role === 'cooperative') {
            return $this->isFpo();
        }

        return $this->role === $role;
    }

    /**
     * Account status checks.
     */
    public function isActive(): bool
    {
        return ($this->status ?? 'active') === 'active';
    }

    public function isSuspended(): bool
    {
        return ($this->status ?? 'active') === 'suspended';
    }

    public function isPending(): bool
    {
        return ($this->status ?? 'active') === 'pending';
    }

    /**
     * Farmer profile details.
     */
    public function farmerProfile()
    {
        return $this->hasOne(FarmerProfile::class, 'user_id');
    }

    /**
     * Buyer profile details.
     */
    public function buyerProfile()
    {
        return $this->hasOne(BuyerProfile::class, 'user_id');
    }

    /**
     * Supplier organizations managed by this user.
     */
    public function supplierOrganizations()
    {
        return $this->belongsToMany(SupplierOrganization::class, 'supplier_members')
            ->withPivot(['role_in_org', 'is_primary_contact'])
            ->withTimestamps();
    }

    public function supplierMemberships()
    {
        return $this->hasMany(SupplierMember::class, 'user_id');
    }

    public function primarySupplierOrganization(): ?SupplierOrganization
    {
        return $this->supplierOrganizations()->first();
    }

    /**
     * FPO organizations managed by this user.
     */
    public function fpoOrganizations()
    {
        return $this->belongsToMany(FpoOrganization::class, 'fpo_members')
            ->withPivot(['role_in_fpo', 'is_primary'])
            ->withTimestamps();
    }

    public function fpoMemberships()
    {
        return $this->hasMany(FpoMember::class, 'user_id');
    }

    public function primaryFpoOrganization(): ?FpoOrganization
    {
        return $this->fpoOrganizations()->first();
    }

    /**
     * Farmer memberships linking this farmer to FPOs.
     */
    public function fpoAffiliations()
    {
        return $this->hasMany(FpoFarmerMembership::class, 'farmer_user_id');
    }

    public function associatedFpos()
    {
        return $this->belongsToMany(FpoOrganization::class, 'fpo_farmer_memberships', 'farmer_user_id', 'fpo_organization_id')
            ->withPivot(['member_code', 'share_units', 'status', 'joined_at', 'notes'])
            ->withTimestamps();
    }

    /**
     * Farmer produce lots.
     */
    public function produceLots()
    {
        return $this->hasMany(ProduceLot::class, 'farmer_id');
    }

    /**
     * Buyer reservations.
     */
    public function produceReservations()
    {
        return $this->hasMany(ProduceReservation::class, 'buyer_id');
    }

    public function procurementRequirements()
    {
        return $this->hasMany(ProcurementRequirement::class, 'buyer_id');
    }

    public function buyerRfqs()
    {
        return $this->hasMany(Rfq::class, 'buyer_id');
    }

    public function sellerQuotations()
    {
        return $this->hasMany(Quotation::class, 'seller_user_id');
    }

    public function buyerPurchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class, 'buyer_id');
    }

    public function sellerPurchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class, 'seller_user_id');
    }

    public function rfqInvitations()
    {
        return $this->hasMany(RfqInvitation::class, 'recipient_user_id');
    }
}
