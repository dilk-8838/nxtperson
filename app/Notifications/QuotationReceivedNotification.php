<?php

namespace App\Notifications;

use App\Models\Quotation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class QuotationReceivedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Quotation $quotation
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $sellerName = $this->quotation->getSellerDisplayName();
        $rfq = $this->quotation->rfq;

        return [
            'type' => 'quotation_received',
            'quotation_id' => $this->quotation->id,
            'quotation_number' => $this->quotation->quotation_number,
            'rfq_id' => $rfq?->id,
            'rfq_number' => $rfq?->rfq_number,
            'title' => "New Quotation Received: #{$this->quotation->quotation_number}",
            'message' => "{$sellerName} quoted ₹".number_format((float) $this->quotation->total_amount, 2)." for RFQ #{$rfq?->rfq_number}.",
            'view_url' => route('rfqs.compare', $rfq->id),
            'action_label' => 'Compare Offers',
            'created_at' => now()->toIso8601String(),
        ];
    }
}
