<?php

namespace App\Notifications;

use App\Models\FpoOrganization;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class FpoVerificationStatusNotification extends Notification
{
    use Queueable;

    public function __construct(
        public FpoOrganization $fpo,
        public string $status,
        public ?string $reason = null
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $statusLabel = match ($this->status) {
            'approved' => 'Approved & Verified',
            'rejected' => 'Application Rejected',
            'suspended' => 'Account Suspended',
            default => ucfirst($this->status),
        };

        $msg = match ($this->status) {
            'approved' => "Congratulations! {$this->fpo->name} has been verified and approved as an authorized FPO / Cooperative on NxtPerson Agritech.",
            'rejected' => "Your FPO registration for '{$this->fpo->name}' was reviewed and rejected. Reason: ".($this->reason ?? 'Information could not be verified.'),
            'suspended' => "Your FPO account for '{$this->fpo->name}' has been temporarily suspended. Reason: ".($this->reason ?? 'Administrative review.'),
            default => "FPO verification status for {$this->fpo->name} has been updated to {$this->status}.",
        };

        return [
            'type' => 'fpo_verification_status',
            'fpo_id' => $this->fpo->id,
            'fpo_name' => $this->fpo->name,
            'status' => $this->status,
            'reason' => $this->reason,
            'title' => "FPO Account {$statusLabel}",
            'message' => $msg,
            'view_url' => route('fpo.dashboard'),
            'action_label' => 'View FPO Portal',
            'created_at' => now()->toIso8601String(),
        ];
    }
}
