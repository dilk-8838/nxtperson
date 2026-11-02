<?php

namespace App\Notifications;

use App\Models\FpoFarmerMembership;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class FpoFarmerMembershipNotification extends Notification
{
    use Queueable;

    public function __construct(
        public FpoFarmerMembership $membership,
        public string $action = 'registered' // registered, updated, deactivated
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
        $fpoName = $this->membership->fpoOrganization ? $this->membership->fpoOrganization->name : 'FPO';
        $code = $this->membership->member_code ?? 'MEMBER';

        $msg = match ($this->action) {
            'registered' => "You have been enrolled as an active farmer member with {$fpoName} (Member Code: {$code}, Shares: {$this->membership->share_units}).",
            'deactivated' => "Your membership with {$fpoName} has been marked as inactive.",
            default => "Your membership details with {$fpoName} have been updated.",
        };

        return [
            'type' => 'fpo_farmer_membership',
            'fpo_id' => $this->membership->fpo_organization_id,
            'fpo_name' => $fpoName,
            'member_code' => $code,
            'status' => $this->membership->status,
            'title' => "FPO Cooperative Membership: {$fpoName}",
            'message' => $msg,
            'view_url' => route('dashboard'),
            'action_label' => 'View Farmer Dashboard',
            'created_at' => now()->toIso8601String(),
        ];
    }
}
