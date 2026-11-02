<?php

namespace App\Policies;

use App\Models\Rfq;
use App\Models\User;

class RfqPolicy
{
    /**
     * Determine whether the user can view the RFQ.
     */
    public function view(User $user, Rfq $rfq): bool
    {
        if ($user->isAdmin() || $user->id === $rfq->buyer_id) {
            return true;
        }

        // Check if user is an invited recipient
        if ($rfq->invitations()->where('recipient_user_id', $user->id)->exists()) {
            return true;
        }

        // Check if user belongs to an invited supplier organization
        $supplierOrgIds = $user->supplierOrganizations()->pluck('supplier_organizations.id')->toArray();
        if (! empty($supplierOrgIds) && $rfq->invitations()->whereIn('supplier_organization_id', $supplierOrgIds)->exists()) {
            return true;
        }

        // Check if user belongs to an invited FPO organization
        $fpoOrgIds = $user->fpoOrganizations()->pluck('fpo_organizations.id')->toArray();
        if (! empty($fpoOrgIds) && $rfq->invitations()->whereIn('fpo_organization_id', $fpoOrgIds)->exists()) {
            return true;
        }

        // If RFQ is an open produce request and user is a farmer/seller
        if ($rfq->produce_lot_id && $rfq->produceLot && $rfq->produceLot->farmer_id === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can compare quotations on the RFQ.
     * STRICT CONFIDENTIALITY: Only the owning buyer or admin can compare!
     */
    public function compare(User $user, Rfq $rfq): bool
    {
        return $user->isAdmin() || $user->id === $rfq->buyer_id;
    }

    /**
     * Determine whether the user can submit a quotation for the RFQ.
     */
    public function submitQuote(User $user, Rfq $rfq): bool
    {
        if (! $rfq->isOpen()) {
            return false;
        }

        // Buyer cannot quote on their own RFQ
        if ($user->id === $rfq->buyer_id) {
            return false;
        }

        // Must be a farmer, supplier, or FPO
        return $user->isFarmer() || $user->isSupplier() || $user->isFpo();
    }

    /**
     * Determine whether the user can close/cancel the RFQ.
     */
    public function update(User $user, Rfq $rfq): bool
    {
        return $user->isAdmin() || $user->id === $rfq->buyer_id;
    }
}
