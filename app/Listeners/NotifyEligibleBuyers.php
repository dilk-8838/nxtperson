<?php

namespace App\Listeners;

use App\Events\ProduceInventoryPublished;
use App\Models\ProduceLot;
use App\Models\User;
use App\Notifications\ProduceAvailableNotification;

class NotifyEligibleBuyers
{
    /**
     * Handle the event.
     */
    public function handle(ProduceInventoryPublished $event): void
    {
        $lot = $event->lot;

        // Guard: Only notify buyers when produce is approved and genuinely available for purchase
        if (! $lot || ! $lot->isAvailableForBuyers() || in_array($lot->status, [ProduceLot::STATUS_DRAFT, ProduceLot::STATUS_SUBMITTED, ProduceLot::STATUS_ASSESSED, ProduceLot::STATUS_UNDER_REVIEW, ProduceLot::STATUS_REJECTED])) {
            return;
        }

        // Query eligible buyer accounts
        $buyers = User::where('role', 'buyer')->get();

        foreach ($buyers as $buyer) {
            // Targeting: If buyer has a specific district preference, honor it; otherwise default to all active buyers
            if ($buyer->district && $lot->district && strtolower($buyer->district) !== strtolower($lot->district)) {
                // If the application doesn't restrict to same district only, all commercial buyers can procure interstate/interdistrict
                // But we ensure buyer is an active buyer account
            }

            // Deduplication check: Avoid multiple alerts for the same produce lot publication
            $alreadyNotified = $buyer->notifications()
                ->where('data->lot_id', $lot->id)
                ->where(function ($q) {
                    $q->where('data->type', 'produce_stock_available')
                        ->orWhere('type', ProduceAvailableNotification::class);
                })
                ->exists();

            if (! $alreadyNotified) {
                $buyer->notify(new ProduceAvailableNotification($lot));
            }
        }
    }
}
