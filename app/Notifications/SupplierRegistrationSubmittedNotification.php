<?php

namespace App\Notifications;

use App\Models\SupplierOrganization;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SupplierRegistrationSubmittedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public SupplierOrganization $supplier
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
            'type' => 'supplier_registration_submitted',
            'supplier_id' => $this->supplier->id,
            'supplier_name' => $this->supplier->name,
            'category' => $this->supplier->category,
            'representative' => $this->supplier->authorized_representative_name,
            'district' => $this->supplier->district,
            'title' => "New Supplier Registration: {$this->supplier->name}",
            'message' => "Supplier '{$this->supplier->name}' ({$this->supplier->category}) in {$this->supplier->district} has registered and submitted credentials for verification.",
            'view_url' => route('admin.suppliers'),
            'action_label' => 'Review Supplier Application',
            'created_at' => now()->toIso8601String(),
        ];
    }
}
