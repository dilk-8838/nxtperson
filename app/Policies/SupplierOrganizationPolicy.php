<?php

namespace App\Policies;

use App\Models\SupplierOrganization;
use App\Models\User;

class SupplierOrganizationPolicy
{
    /**
     * Determine whether the user can view the supplier organization.
     */
    public function view(User $user, SupplierOrganization $supplier): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $supplier->hasMember($user);
    }

    /**
     * Determine whether the user can update the supplier organization.
     */
    public function update(User $user, SupplierOrganization $supplier): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $supplier->supplierMembers()
            ->where('user_id', $user->id)
            ->whereIn('role_in_org', ['owner', 'manager'])
            ->exists();
    }

    /**
     * Determine whether the user can manage team members.
     */
    public function manageMembers(User $user, SupplierOrganization $supplier): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $supplier->supplierMembers()
            ->where('user_id', $user->id)
            ->where('role_in_org', 'owner')
            ->exists();
    }
}
