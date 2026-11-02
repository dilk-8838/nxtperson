<?php

namespace App\Notifications;

use App\Models\FpoGateEntry;
use App\Models\FpoInventoryPosting;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class InventoryPostedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public FpoGateEntry $entry,
        public FpoInventoryPosting $posting
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'inventory_posted',
            'entry_id' => $this->entry->id,
            'entry_number' => $this->entry->entry_number,
            'posting_id' => $this->posting->id,
            'posting_number' => $this->posting->posting_number,
            'title' => "Inventory Posted: {$this->posting->posted_quantity_kg} kg Added",
            'message' => "Accepted produce of {$this->posting->posted_quantity_kg} kg from Entry #{$this->entry->entry_number} has been posted into FPO inventory (Ref: #{$this->posting->posting_number}).",
            'view_url' => route('fpo.gate-entries.show', $this->entry->id),
            'action_label' => 'View Inventory Reconciliation',
            'created_at' => now()->toIso8601String(),
        ];
    }
}
