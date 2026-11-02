<?php

namespace App\Policies;

use App\Models\FpoGateEntry;
use App\Models\User;

class FpoGateEntryPolicy
{
    /**
     * Determine whether user can view the gate operations index.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isFpo();
    }

    /**
     * Determine whether user can view the gate entry.
     */
    public function view(User $user, FpoGateEntry $entry): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isFpo()) {
            return $user->fpoOrganizations()
                ->where('fpo_organizations.id', $entry->fpo_organization_id)
                ->exists();
        }

        if ($entry->farmer_user_id && $user->id === $entry->farmer_user_id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether user can create gate entries.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isFpo();
    }

    /**
     * Determine whether user can update the gate entry.
     */
    public function update(User $user, FpoGateEntry $entry): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isFpo()) {
            return $user->fpoOrganizations()
                ->where('fpo_organizations.id', $entry->fpo_organization_id)
                ->exists();
        }

        return false;
    }

    /**
     * Determine whether user can record weightment.
     */
    public function recordWeightment(User $user, FpoGateEntry $entry): bool
    {
        return $this->update($user, $entry);
    }

    /**
     * Determine whether user can collect samples.
     */
    public function collectSample(User $user, FpoGateEntry $entry): bool
    {
        return $this->update($user, $entry);
    }

    /**
     * Determine whether user can record quality test results.
     */
    public function recordQualityTest(User $user, FpoGateEntry $entry): bool
    {
        return $this->update($user, $entry);
    }

    /**
     * Determine whether user can approve or reject quality.
     */
    public function approveQuality(User $user, FpoGateEntry $entry): bool
    {
        if (! $this->update($user, $entry)) {
            return false;
        }

        // Optional separation of duties enforcement
        if (config('agritech.enforce_separation_of_duties', false)) {
            $isTester = $entry->samples()
                ->whereHas('qualityTests', fn ($q) => $q->where('tested_by', $user->id))
                ->exists();

            if ($isTester) {
                return false;
            }
        }

        return true;
    }

    /**
     * Determine whether user can post accepted produce to inventory.
     */
    public function postInventory(User $user, FpoGateEntry $entry): bool
    {
        return $this->update($user, $entry);
    }

    /**
     * Determine whether user can authorize exit.
     */
    public function authorizeExit(User $user, FpoGateEntry $entry): bool
    {
        return $this->update($user, $entry);
    }

    /**
     * Determine whether user can export or download individual entry report / PDF.
     */
    public function exportReport(User $user, FpoGateEntry $entry): bool
    {
        return $this->view($user, $entry);
    }
}
