<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    /**
     * Determine whether the user can view the product.
     */
    public function view(?User $user, Product $product): bool
    {
        if ($product->isAvailable()) {
            return true;
        }

        if (! $user) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        if ($product->supplier_organization_id) {
            return $user->supplierOrganizations()->where('supplier_organizations.id', $product->supplier_organization_id)->exists();
        }

        return false;
    }

    /**
     * Determine whether the user can update the product.
     */
    public function update(User $user, Product $product): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if (! $product->supplier_organization_id) {
            return false;
        }

        return $user->supplierOrganizations()->where('supplier_organizations.id', $product->supplier_organization_id)->exists();
    }

    /**
     * Determine whether the user can delete the product.
     */
    public function delete(User $user, Product $product): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if (! $product->supplier_organization_id) {
            return false;
        }

        // Only owner or manager in supplier organization can delete
        return $user->supplierMembers()
            ->where('supplier_organization_id', $product->supplier_organization_id)
            ->whereIn('role_in_org', ['owner', 'manager'])
            ->exists();
    }
}
