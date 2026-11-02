<?php

namespace App\Notifications;

use App\Models\Rfq;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class RfqCreatedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Rfq $rfq,
        public User $buyer
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $buyerName = $this->buyer->name ?: 'Enterprise Buyer';

        return [
            'type' => 'rfq_created',
            'rfq_id' => $this->rfq->id,
            'rfq_number' => $this->rfq->rfq_number,
            'title' => "New RFQ Received: {$this->rfq->title} (#{$this->rfq->rfq_number})",
            'message' => "{$buyerName} requested a quotation for {$this->rfq->quantity} {$this->rfq->unit} in {$this->rfq->delivery_district}.",
            'view_url' => route('rfqs.show', $this->rfq->id),
            'action_label' => 'Submit Quotation',
            'created_at' => now()->toIso8601String(),
        ];
    }
}
