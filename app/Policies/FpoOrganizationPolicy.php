<?php

namespace App\Policies;

use App\Models\FpoOrganization;
use App\Models\User;

class FpoOrganizationPolicy
{
    /**
     * Determine whether the user can view the FPO details and private roster.
     */
    public function view(User $user, FpoOrganization $fpo): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $fpo->hasStaffMember($user) || $fpo->memberFarmers()->where('users.id', $user->id)->exists();
    }

    /**
     * Determine whether the user can update the FPO profile.
     */
    public function update(User $user, FpoOrganization $fpo): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $fpo->fpoMembers()
            ->where('user_id', $user->id)
            ->whereIn('role_in_fpo', ['admin', 'board_director', 'ceo'])
            ->exists();
    }

    /**
     * Determine whether the user can manage farmer memberships.
     */
    public function manageMembers(User $user, FpoOrganization $fpo): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $fpo->fpoMembers()
            ->where('user_id', $user->id)
            ->whereIn('role_in_fpo', ['admin', 'board_director', 'ceo', 'manager'])
            ->exists();
    }
}
