<?php

namespace App\Notifications;

use App\Models\FpoGateEntry;
use App\Models\FpoQualityApproval;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class QualityDecisionNotification extends Notification
{
    use Queueable;

    public function __construct(
        public FpoGateEntry $entry,
        public FpoQualityApproval $approval
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $decisionLabel = ucfirst(str_replace('_', ' ', $this->approval->decision));

        return [
            'type' => 'quality_decision_recorded',
            'entry_id' => $this->entry->id,
            'entry_number' => $this->entry->entry_number,
            'approval_id' => $this->approval->id,
            'decision' => $this->approval->decision,
            'title' => "Quality Decision: {$decisionLabel} for #{$this->entry->entry_number}",
            'message' => "Produce delivery #{$this->entry->entry_number} has been {$decisionLabel} by {$this->approval->approver_name} ({$this->approval->approver_role}). Accepted: {$this->approval->accepted_quantity} kg, Rejected: {$this->approval->rejected_quantity} kg.",
            'view_url' => route('fpo.gate-entries.show', $this->entry->id),
            'action_label' => 'View Entry',
            'created_at' => now()->toIso8601String(),
        ];
    }
}
