<?php

namespace App\Notifications;

use App\Models\FpoGateEntry;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class QualityApprovalRequestedNotification extends Notification
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
            'type' => 'quality_approval_requested',
            'entry_id' => $this->entry->id,
            'entry_number' => $this->entry->entry_number,
            'title' => "Quality Sign-Off Required: #{$this->entry->entry_number}",
            'message' => "Laboratory tests for Entry #{$this->entry->entry_number} ({$this->entry->crop_name}) have been completed. Authorized approval is required.",
            'view_url' => route('fpo.gate-entries.show', $this->entry->id),
            'action_label' => 'Review & Sign Off',
            'created_at' => now()->toIso8601String(),
        ];
    }
}
