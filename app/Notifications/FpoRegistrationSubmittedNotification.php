<?php

namespace App\Notifications;

use App\Models\FpoOrganization;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class FpoRegistrationSubmittedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public FpoOrganization $fpo
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
        return [
            'type' => 'fpo_registration_submitted',
            'fpo_id' => $this->fpo->id,
            'fpo_name' => $this->fpo->name,
            'organization_type' => $this->fpo->organization_type,
            'representative' => $this->fpo->authorized_representative_name,
            'district' => $this->fpo->district,
            'title' => "New FPO Registration: {$this->fpo->name}",
            'message' => "FPO '{$this->fpo->name}' ({$this->fpo->organization_type}) in {$this->fpo->district} has registered and submitted credentials for admin verification.",
            'view_url' => route('admin.fpos'),
            'action_label' => 'Review FPO Application',
            'created_at' => now()->toIso8601String(),
        ];
    }
}
