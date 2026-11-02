<?php

namespace App\Notifications;

use App\Models\SupplierOrganization;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SupplierVerificationStatusNotification extends Notification
{
    use Queueable;

    public function __construct(
        public SupplierOrganization $supplier,
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
            'approved' => "Congratulations! {$this->supplier->name} has been verified and approved as an authorized agri-input supplier on the NxtPerson platform.",
            'rejected' => "Your supplier application for '{$this->supplier->name}' was reviewed and rejected. Reason: ".($this->reason ?? 'Information could not be verified.'),
            'suspended' => "Your supplier business '{$this->supplier->name}' has been temporarily suspended. Reason: ".($this->reason ?? 'Administrative review.'),
            default => "Supplier status for {$this->supplier->name} has been updated to {$this->status}.",
        };

        return [
            'type' => 'supplier_verification_status',
            'supplier_id' => $this->supplier->id,
            'supplier_name' => $this->supplier->name,
            'status' => $this->status,
            'reason' => $this->reason,
            'title' => "Supplier Account {$statusLabel}",
            'message' => $msg,
            'view_url' => route('supplier.dashboard'),
            'action_label' => 'View Supplier Dashboard',
            'created_at' => now()->toIso8601String(),
        ];
    }
}
