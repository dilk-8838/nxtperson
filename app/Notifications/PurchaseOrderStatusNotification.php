<?php

namespace App\Notifications;

use App\Models\PurchaseOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PurchaseOrderStatusNotification extends Notification
{
    use Queueable;

    public function __construct(
        public PurchaseOrder $order,
        public string $previousStatus
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $statusLabel = $this->order->getStatusLabel();

        return [
            'type' => 'purchase_order_status_updated',
            'order_id' => $this->order->id,
            'po_number' => $this->order->po_number,
            'status' => $this->order->status,
            'title' => "Purchase Order #{$this->order->po_number}: {$statusLabel}",
            'message' => "Order #{$this->order->po_number} ({$this->order->item_description}) status updated to {$statusLabel}.",
            'view_url' => route('orders.show', $this->order->id),
            'action_label' => 'View Purchase Order',
            'created_at' => now()->toIso8601String(),
        ];
    }
}
