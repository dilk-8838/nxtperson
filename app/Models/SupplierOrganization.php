<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupplierOrganization extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REJECTED = 'rejected';

    public const STATUS_SUSPENDED = 'suspended';

    protected $fillable = [
        'name',
        'slug',
        'category',
        'description',
        'gst_number',
        'license_number',
        'authorized_representative_name',
        'contact_email',
        'contact_phone',
        'operating_address',
        'district',
        'state',
        'pincode',
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
            'verified_at' => 'datetime',
        ];
    }

    /**
     * Authorized team members pivot records.
     */
    public function supplierMembers(): HasMany
    {
        return $this->hasMany(SupplierMember::class, 'supplier_organization_id');
    }

    /**
     * Users associated with this supplier business.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'supplier_members')
            ->withPivot(['role_in_org', 'is_primary_contact'])
            ->withTimestamps();
    }

    public function primaryContact(): ?User
    {
        return $this->users()->wherePivot('is_primary_contact', true)->first()
            ?? $this->users()->first();
    }

    /**
     * Admin who verified or reviewed this supplier.
     */
    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Check if supplier is approved for full marketplace capability.
     */
    public function isApproved(): bool
    {
        return $this->verification_status === self::STATUS_APPROVED;
    }

    /**
     * Check if supplier verification is pending.
     */
    public function isPending(): bool
    {
        return $this->verification_status === self::STATUS_PENDING;
    }

    /**
     * Check if user is an authorized member of this supplier business.
     */
    public function hasMember(User $user): bool
    {
        return $this->supplierMembers()->where('user_id', $user->id)->exists();
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'supplier_organization_id');
    }

    public function quotations(): HasMany
    {
        return $this->hasMany(Quotation::class, 'supplier_organization_id');
    }

    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class, 'supplier_organization_id');
    }

    public function rfqInvitations(): HasMany
    {
        return $this->hasMany(RfqInvitation::class, 'supplier_organization_id');
    }
}
