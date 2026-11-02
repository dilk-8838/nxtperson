<?php

namespace App\Policies;

use App\Models\FpoGateExit;
use App\Models\User;

class FpoGateExitPolicy
{
    /**
     * Determine whether user can view the gate exit.
     */
    public function view(User $user, FpoGateExit $exit): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isFpo()) {
            return $user->fpoOrganizations()
                ->where('fpo_organizations.id', $exit->fpo_organization_id)
                ->exists();
        }

        return false;
    }

    /**
     * Determine whether user can authorize gate exit.
     */
    public function authorize(User $user, FpoGateExit $exit): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isFpo()) {
            return $user->fpoOrganizations()
                ->where('fpo_organizations.id', $exit->fpo_organization_id)
                ->exists();
        }

        return false;
    }

    /**
     * Determine whether user can confirm handover / exit.
     */
    public function confirmHandover(User $user, FpoGateExit $exit): bool
    {
        return $this->authorize($user, $exit);
    }
}
