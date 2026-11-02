<?php

namespace App\Notifications;

use App\Models\Quotation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class QuotationStatusNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Quotation $quotation,
        public string $actionType // 'accepted', 'declined'
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $statusLabel = $this->actionType === 'accepted' ? 'Accepted' : 'Declined';
        $rfq = $this->quotation->rfq;

        return [
            'type' => 'quotation_status_updated',
            'quotation_id' => $this->quotation->id,
            'quotation_number' => $this->quotation->quotation_number,
            'rfq_id' => $rfq?->id,
            'status' => $this->quotation->status,
            'title' => "Quotation #{$this->quotation->quotation_number} {$statusLabel}",
            'message' => "The buyer has {$this->actionType} your quotation for RFQ #{$rfq?->rfq_number} (₹".number_format((float) $this->quotation->total_amount, 2).').',
            'view_url' => route('quotations.show', $this->quotation->id),
            'action_label' => 'View Quotation',
            'created_at' => now()->toIso8601String(),
        ];
    }
}
