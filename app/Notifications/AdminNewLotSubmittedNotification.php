<?php

namespace App\Notifications;

use App\Models\ProduceLot;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AdminNewLotSubmittedNotification extends Notification
{
    use Queueable;

    public function __construct(
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
        $farmerName = $this->lot->farmer ? $this->lot->farmer->name : 'Farmer';

        return [
            'type' => 'admin_new_lot_submitted',
            'lot_id' => $this->lot->id,
            'lot_number' => $this->lot->lot_number,
            'crop_slug' => $this->lot->crop_slug,
            'crop_name' => $cropName,
            'farmer_name' => $farmerName,
            'district' => $this->lot->district,
            'quantity_in_kg' => (float) $this->lot->quantity_in_kg,
            'title' => "Produce Lot for Triage: {$cropName} (Lot #{$this->lot->lot_number})",
            'message' => "New harvest lot submitted by {$farmerName} from {$this->lot->district} (".number_format((float) $this->lot->quantity_in_kg, 1)." kg of {$this->lot->variety}). Requires verification & price approval.",
            'view_url' => route('admin.produce.show', $this->lot->id),
            'action_label' => 'Review & Grade Produce',
            'created_at' => now()->toIso8601String(),
        ];
    }
}
