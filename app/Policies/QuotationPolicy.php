<?php

namespace App\Policies;

use App\Models\Quotation;
use App\Models\User;

class QuotationPolicy
{
    /**
     * Determine whether the user can view the quotation.
     * STRICT CONFIDENTIALITY: Competitors cannot view!
     */
    public function view(User $user, Quotation $quotation): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        // RFQ buyer can view quotations submitted on their RFQ
        if ($quotation->rfq && $quotation->rfq->buyer_id === $user->id) {
            return true;
        }

        // The seller user who submitted the quote
        if ($quotation->seller_user_id === $user->id) {
            return true;
        }

        // Member of supplier organization
        if ($quotation->supplier_organization_id) {
            if ($user->supplierOrganizations()->where('supplier_organizations.id', $quotation->supplier_organization_id)->exists()) {
                return true;
            }
        }

        // Member of FPO organization
        if ($quotation->fpo_organization_id) {
            if ($user->fpoOrganizations()->where('fpo_organizations.id', $quotation->fpo_organization_id)->exists()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine whether the user can accept the quotation.
     */
    public function accept(User $user, Quotation $quotation): bool
    {
        if (! $quotation->isAcceptable()) {
            return false;
        }

        return $user->isAdmin() || ($quotation->rfq && $quotation->rfq->buyer_id === $user->id);
    }

    /**
     * Determine whether the user can withdraw/update the quotation.
     */
    public function update(User $user, Quotation $quotation): bool
    {
        if (! $quotation->isPending()) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        if ($quotation->seller_user_id === $user->id) {
            return true;
        }

        if ($quotation->supplier_organization_id) {
            return $user->supplierOrganizations()->where('supplier_organizations.id', $quotation->supplier_organization_id)->exists();
        }

        if ($quotation->fpo_organization_id) {
            return $user->fpoOrganizations()->where('fpo_organizations.id', $quotation->fpo_organization_id)->exists();
        }

        return false;
    }
}
