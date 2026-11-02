<?php

namespace App\Notifications;

use App\Models\ProduceReservation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class FarmerReservationStatusNotification extends Notification
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

        $statusLabels = [
            'confirmed' => 'Confirmed',
            'fulfilled' => 'Fulfilled & Dispatched',
            'cancelled' => 'Cancelled',
        ];
        $label = $statusLabels[$this->newStatus] ?? ucfirst($this->newStatus);

        $messages = [
            'fulfilled' => "Buyer reservation #{$this->reservation->reservation_number} (".number_format((float) $this->reservation->quantity_kg, 1).' kg) has been fulfilled.',
            'cancelled' => "Buyer reservation #{$this->reservation->reservation_number} (".number_format((float) $this->reservation->quantity_kg, 1).' kg) was cancelled and inventory restored.',
            'confirmed' => "Buyer reservation #{$this->reservation->reservation_number} has been confirmed.",
        ];

        return [
            'type' => 'farmer_reservation_status',
            'reservation_id' => $this->reservation->id,
            'reservation_number' => $this->reservation->reservation_number,
            'lot_id' => $lot ? $lot->id : null,
            'status' => $this->newStatus,
            'title' => "Order Status: #{$this->reservation->reservation_number} ({$label})",
            'message' => $messages[$this->newStatus] ?? "Reservation status updated to {$label}.",
            'view_url' => $lot ? route('farmer.lots.show', $lot->id) : route('farmer.lots.index'),
            'action_label' => 'View Produce Details',
            'created_at' => now()->toIso8601String(),
        ];
    }
}
