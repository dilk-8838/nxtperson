<?php

namespace App\Policies;

use App\Models\ProduceLot;
use App\Models\User;

class ProduceLotPolicy
{
    /**
     * Determine whether the user can view the produce lot.
     */
    public function view(?User $user, ProduceLot $lot): bool
    {
        if ($lot->isAvailableForBuyers()) {
            return true;
        }

        if (! $user) {
            return false;
        }

        return $user->isAdmin() || $user->id === $lot->farmer_id;
    }

    /**
     * Determine whether the user can update the produce lot.
     */
    public function update(User $user, ProduceLot $lot): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->id !== $lot->farmer_id) {
            return false;
        }

        // Farmers can edit if not completely sold out or cancelled
        return ! in_array($lot->status, [ProduceLot::STATUS_SOLD_OUT, ProduceLot::STATUS_CANCELLED]);
    }

    /**
     * Determine whether the user can withdraw/cancel the produce lot.
     */
    public function withdraw(User $user, ProduceLot $lot): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->id !== $lot->farmer_id) {
            return false;
        }

        return ! in_array($lot->status, [ProduceLot::STATUS_SOLD_OUT, ProduceLot::STATUS_CANCELLED]);
    }

    /**
     * Determine whether the user can delete the produce lot.
     */
    public function delete(User $user, ProduceLot $lot): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->id === $lot->farmer_id && in_array($lot->status, [ProduceLot::STATUS_DRAFT, ProduceLot::STATUS_SUBMITTED]);
    }
}
