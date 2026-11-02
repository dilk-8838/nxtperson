<?php

namespace App\Notifications;

use App\Models\ProduceLot;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class FarmerProduceInterestNotification extends Notification
{
    use Queueable;

    public function __construct(
        public ProduceLot $lot,
        public User $buyer,
        public ?string $inquiryNote = null
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
     * Sanitized to avoid exposing sensitive buyer credentials.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $cropName = $this->lot->crop ? $this->lot->crop->en_name : ucfirst($this->lot->crop_slug);
        $buyerDisplay = $this->buyer->name ?: 'Verified Commercial Buyer';
        if ($this->buyer->district) {
            $buyerDisplay .= " ({$this->buyer->district})";
        }

        $noteSnippet = $this->inquiryNote ? ": \"{$this->inquiryNote}\"" : '.';

        return [
            'type' => 'farmer_interest',
            'lot_id' => $this->lot->id,
            'lot_number' => $this->lot->lot_number,
            'crop_slug' => $this->lot->crop_slug,
            'crop_name' => $cropName,
            'buyer_id' => $this->buyer->id,
            'buyer_name' => $buyerDisplay,
            'title' => "Buyer Interest: {$cropName} (Lot #{$this->lot->lot_number})",
            'message' => "{$buyerDisplay} expressed interest in your {$this->lot->variety} {$cropName}{$noteSnippet}",
            'view_url' => route('farmer.lots.show', $this->lot->id),
            'action_label' => 'View Listing Details',
            'created_at' => now()->toIso8601String(),
        ];
    }
}
