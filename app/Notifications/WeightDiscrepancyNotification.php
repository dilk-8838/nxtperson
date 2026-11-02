<?php

namespace App\Notifications;

use App\Models\FpoGateEntry;
use App\Models\FpoWeightment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class WeightDiscrepancyNotification extends Notification
{
    use Queueable;

    public function __construct(
        public FpoGateEntry $entry,
        public FpoWeightment $weightment
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'weight_discrepancy_alert',
            'entry_id' => $this->entry->id,
            'entry_number' => $this->entry->entry_number,
            'weightment_id' => $this->weightment->id,
            'title' => "Weight Discrepancy on Entry #{$this->entry->entry_number}",
            'message' => "Measured net weight ({$this->weightment->net_weight} {$this->weightment->unit}) diverges by {$this->weightment->discrepancy_percentage}% from declared quantity ({$this->entry->declared_quantity} {$this->entry->declared_unit}).",
            'view_url' => route('fpo.gate-entries.show', $this->entry->id),
            'action_label' => 'Review Discrepancy',
            'created_at' => now()->toIso8601String(),
        ];
    }
}
