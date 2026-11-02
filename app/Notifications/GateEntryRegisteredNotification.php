<?php

namespace App\Notifications;

use App\Models\FpoGateEntry;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class GateEntryRegisteredNotification extends Notification
{
    use Queueable;

    public function __construct(
        public FpoGateEntry $entry
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'gate_entry_registered',
            'entry_id' => $this->entry->id,
            'entry_number' => $this->entry->entry_number,
            'title' => "Gate Entry #{$this->entry->entry_number} Registered",
            'message' => "Delivery of {$this->entry->declared_quantity} {$this->entry->declared_unit} of {$this->entry->crop_name} from {$this->entry->delivering_party_name} registered at {$this->entry->gate_point}.",
            'view_url' => route('fpo.gate-entries.show', $this->entry->id),
            'action_label' => 'View Gate Entry',
            'created_at' => now()->toIso8601String(),
        ];
    }
}
