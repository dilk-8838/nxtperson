<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplierMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_organization_id',
        'user_id',
        'role_in_org',
        'is_primary_contact',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_primary_contact' => 'boolean',
        ];
    }

    /**
     * Supplier organization.
     */
    public function supplierOrganization(): BelongsTo
    {
        return $this->belongsTo(SupplierOrganization::class, 'supplier_organization_id');
    }

    /**
     * User account.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
