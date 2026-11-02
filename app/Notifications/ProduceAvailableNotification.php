<?php

namespace App\Notifications;

use App\Models\ProduceLot;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ProduceAvailableNotification extends Notification
{
    use Queueable;

    public ProduceLot $lot;

    /**
     * Create a new notification instance.
     */
    public function __construct(ProduceLot $lot)
    {
        $this->lot = $lot;
    }

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
     * Sanitized to NEVER leak farmer personal identifiable information.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $cropName = $this->lot->crop ? $this->lot->crop->en_name : ucfirst($this->lot->crop_slug);
        $primaryImg = $this->lot->primaryImage;
        $imageUrl = $primaryImg ? asset($primaryImg->image_path) : asset('images/crops/'.$this->lot->crop_slug.'.jpg');

        return [
            'type' => 'produce_stock_available',
            'lot_id' => $this->lot->id,
            'lot_number' => $this->lot->lot_number,
            'crop_slug' => $this->lot->crop_slug,
            'crop_name' => $cropName,
            'variety' => $this->lot->variety,
            'grade' => $this->lot->approved_grade ?? 'Good',
            'available_quantity_kg' => (float) $this->lot->available_quantity_kg,
            'district' => $this->lot->district,
            'buyer_sale_price_per_kg' => (float) ($this->lot->buyer_sale_price_per_kg ?? 0),
            'image_url' => $imageUrl,
            'title' => "New Verified Produce Available: {$cropName} ({$this->lot->approved_grade})",
            'message' => number_format((float) $this->lot->available_quantity_kg, 1)." kg of certified {$this->lot->variety} {$cropName} from {$this->lot->district} is now open for buyer procurement at ₹".number_format((float) ($this->lot->buyer_sale_price_per_kg ?? 0), 2).'/kg.',
            'view_url' => route('buyer.lot.show', $this->lot->id),
            'action_label' => 'View Produce & Order',
            'published_at' => now()->toIso8601String(),
        ];
    }
}
