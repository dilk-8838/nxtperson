<?php

namespace App\Policies;

use App\Models\FpoOperationalException;
use App\Models\User;

class FpoOperationalExceptionPolicy
{
    public function view(User $user, FpoOperationalException $exception): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isFpo()) {
            return $user->fpoOrganizations()
                ->where('fpo_organizations.id', $exception->fpo_organization_id)
                ->exists();
        }

        return false;
    }

    public function resolve(User $user, FpoOperationalException $exception): bool
    {
        return $this->view($user, $exception);
    }
}
