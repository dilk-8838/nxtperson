<?php

namespace App\Policies;

use App\Models\FarmerProfile;
use App\Models\User;

class FarmerProfilePolicy
{
    /**
     * Determine whether the user can view the farmer profile.
     */
    public function view(User $user, FarmerProfile $profile): bool
    {
        if ($user->isAdmin() || $user->id === $profile->user_id) {
            return true;
        }

        // FPO staff where farmer is a member
        $fpoIds = $user->fpoOrganizations()->pluck('fpo_organizations.id');
        if ($fpoIds->isNotEmpty()) {
            return $profile->user->fpoAffiliations()
                ->whereIn('fpo_organization_id', $fpoIds)
                ->exists();
        }

        return false;
    }

    /**
     * Determine whether the user can update the farmer profile.
     */
    public function update(User $user, FarmerProfile $profile): bool
    {
        return $user->isAdmin() || $user->id === $profile->user_id;
    }
}
