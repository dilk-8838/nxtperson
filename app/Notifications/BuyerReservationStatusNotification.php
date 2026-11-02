<?php

namespace App\Notifications;

use App\Models\ProduceReservation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BuyerReservationStatusNotification extends Notification
{
    use Queueable;

    public function __construct(
        public ProduceReservation $reservation,
        public string $newStatus
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

        $statusTitles = [
            'confirmed' => "Order Confirmed: #{$this->reservation->reservation_number}",
            'fulfilled' => "Order Dispatched / Fulfilled: #{$this->reservation->reservation_number}",
            'cancelled' => "Order Cancelled: #{$this->reservation->reservation_number}",
        ];

        $statusMessages = [
            'confirmed' => "Your reservation #{$this->reservation->reservation_number} for ".number_format((float) $this->reservation->quantity_kg, 1)." kg of {$cropName} has been confirmed.",
            'fulfilled' => "Your procurement order #{$this->reservation->reservation_number} (".number_format((float) $this->reservation->quantity_kg, 1)." kg of {$cropName}) has been fulfilled and prepared for depot pickup/dispatch.",
            'cancelled' => "Your reservation #{$this->reservation->reservation_number} (".number_format((float) $this->reservation->quantity_kg, 1)." kg of {$cropName}) has been cancelled.",
        ];

        return [
            'type' => 'buyer_reservation_status',
            'reservation_id' => $this->reservation->id,
            'reservation_number' => $this->reservation->reservation_number,
            'lot_id' => $lot ? $lot->id : null,
            'crop_name' => $cropName,
            'status' => $this->newStatus,
            'quantity_kg' => (float) $this->reservation->quantity_kg,
            'title' => $statusTitles[$this->newStatus] ?? "Order #{$this->reservation->reservation_number} Updated",
            'message' => $statusMessages[$this->newStatus] ?? 'Your order status was updated to '.ucfirst($this->newStatus).'.',
            'view_url' => route('buyer.dashboard'),
            'action_label' => 'View in Dashboard',
            'created_at' => now()->toIso8601String(),
        ];
    }
}
