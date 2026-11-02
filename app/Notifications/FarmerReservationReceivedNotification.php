<?php

namespace App\Notifications;

use App\Models\ProduceReservation;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class FarmerReservationReceivedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public ProduceReservation $reservation,
        public User $buyer
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
        $lot = $this->reservation->lot;
        $cropName = $lot && $lot->crop ? $lot->crop->en_name : ($lot ? ucfirst($lot->crop_slug) : 'Produce');
        $buyerDisplay = $this->buyer->name ?: 'Verified Commercial Buyer';

        return [
            'type' => 'farmer_reservation_received',
            'reservation_id' => $this->reservation->id,
            'reservation_number' => $this->reservation->reservation_number,
            'lot_id' => $lot ? $lot->id : null,
            'lot_number' => $lot ? $lot->lot_number : null,
            'crop_name' => $cropName,
            'buyer_name' => $buyerDisplay,
            'quantity_kg' => (float) $this->reservation->quantity_kg,
            'price_per_kg' => (float) $this->reservation->price_per_kg,
            'total_price' => (float) $this->reservation->total_price,
            'title' => "New Purchase Request: {$cropName} (Order #{$this->reservation->reservation_number})",
            'message' => "{$buyerDisplay} reserved ".number_format((float) $this->reservation->quantity_kg, 1)." kg of {$cropName} for ₹".number_format((float) $this->reservation->total_price, 2)." (Lot #{$lot?->lot_number}).",
            'view_url' => $lot ? route('farmer.lots.show', $lot->id) : route('farmer.lots.index'),
            'action_label' => 'View Produce Lot',
            'created_at' => now()->toIso8601String(),
        ];
    }
}
