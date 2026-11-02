<?php

namespace App\Policies;

use App\Models\PurchaseOrder;
use App\Models\User;

class PurchaseOrderPolicy
{
    /**
     * Determine whether the user can view the purchase order.
     */
    public function view(User $user, PurchaseOrder $po): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($po->buyer_id === $user->id || $po->seller_user_id === $user->id) {
            return true;
        }

        if ($po->supplier_organization_id) {
            if ($user->supplierOrganizations()->where('supplier_organizations.id', $po->supplier_organization_id)->exists()) {
                return true;
            }
        }

        if ($po->fpo_organization_id) {
            if ($user->fpoOrganizations()->where('fpo_organizations.id', $po->fpo_organization_id)->exists()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine whether the user can update the purchase order status.
     */
    public function updateStatus(User $user, PurchaseOrder $po): bool
    {
        return $this->view($user, $po);
    }

    /**
     * Determine whether the user can cancel the purchase order.
     */
    public function cancel(User $user, PurchaseOrder $po): bool
    {
        if (! $po->canBeCancelled()) {
            return false;
        }

        return $user->isAdmin() || $po->buyer_id === $user->id || $po->seller_user_id === $user->id;
    }
}
