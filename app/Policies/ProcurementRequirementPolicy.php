<?php

namespace App\Policies;

use App\Models\ProcurementRequirement;
use App\Models\User;

class ProcurementRequirementPolicy
{
    /**
     * Determine whether the user can view the requirement.
     */
    public function view(User $user, ProcurementRequirement $requirement): bool
    {
        return $user->isAdmin() || $user->id === $requirement->buyer_id;
    }

    /**
     * Determine whether the user can update the requirement.
     */
    public function update(User $user, ProcurementRequirement $requirement): bool
    {
        return $user->isAdmin() || $user->id === $requirement->buyer_id;
    }

    /**
     * Determine whether the user can delete/cancel the requirement.
     */
    public function delete(User $user, ProcurementRequirement $requirement): bool
    {
        return $user->isAdmin() || $user->id === $requirement->buyer_id;
    }
}
