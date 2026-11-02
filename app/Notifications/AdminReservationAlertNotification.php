<?php

namespace App\Notifications;

use App\Models\ProduceLot;
use App\Models\ProduceReservation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AdminReservationAlertNotification extends Notification
{
    use Queueable;

    public function __construct(
        public ProduceReservation $reservation,
        public ProduceLot $lot
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
        $cropName = $this->lot->crop ? $this->lot->crop->en_name : ucfirst($this->lot->crop_slug);
        $buyerName = $this->reservation->buyer ? $this->reservation->buyer->name : 'Buyer';

        $stockNote = $this->lot->available_quantity_kg <= 0
            ? ' Produce lot is now completely SOLD OUT.'
            : ' Remaining inventory: '.number_format((float) $this->lot->available_quantity_kg, 1).' kg.';

        return [
            'type' => 'admin_reservation_alert',
            'reservation_id' => $this->reservation->id,
            'reservation_number' => $this->reservation->reservation_number,
            'lot_id' => $this->lot->id,
            'lot_number' => $this->lot->lot_number,
            'crop_name' => $cropName,
            'buyer_name' => $buyerName,
            'quantity_kg' => (float) $this->reservation->quantity_kg,
            'total_price' => (float) $this->reservation->total_price,
            'title' => "Buyer Order Booked: #{$this->reservation->reservation_number} ({$cropName})",
            'message' => "{$buyerName} reserved ".number_format((float) $this->reservation->quantity_kg, 1).' kg (₹'.number_format((float) $this->reservation->total_price, 2).").{$stockNote}",
            'view_url' => route('admin.produce.reservations'),
            'action_label' => 'View Reservations Hub',
            'created_at' => now()->toIso8601String(),
        ];
    }
}
