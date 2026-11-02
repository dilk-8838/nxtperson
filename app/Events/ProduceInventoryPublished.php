<?php

namespace App\Events;

use App\Models\ProduceLot;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProduceInventoryPublished implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public ProduceLot $lot;

    /**
     * Create a new event instance.
     */
    public function __construct(ProduceLot $lot)
    {
        $this->lot = $lot;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('produce.available'),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'ProduceInventoryPublished';
    }

    /**
     * Get the data that should be sent with the broadcasted event.
     * Guaranteed strictly scrubbed of confidential farmer identity.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        $cropName = $this->lot->crop ? $this->lot->crop->en_name : ucfirst($this->lot->crop_slug);
        $primaryImg = $this->lot->primaryImage;
        $imageUrl = $primaryImg ? asset($primaryImg->image_path) : asset('images/crops/'.$this->lot->crop_slug.'.jpg');

        return [
            'lot_id' => $this->lot->id,
            'lot_number' => $this->lot->lot_number,
            'crop_slug' => $this->lot->crop_slug,
            'crop_name' => $cropName,
            'variety' => $this->lot->variety,
            'grade' => $this->lot->approved_grade ?? 'Good',
            'available_quantity_kg' => (float) $this->lot->available_quantity_kg,
            'formatted_quantity' => number_format((float) $this->lot->available_quantity_kg, 1).' kg ('.number_format((float) $this->lot->available_quantity_kg / 100, 1).' Qtl)',
            'district' => $this->lot->district,
            'buyer_sale_price_per_kg' => (float) ($this->lot->buyer_sale_price_per_kg ?? 0),
            'image_url' => $imageUrl,
            'view_url' => route('buyer.lot.show', $this->lot->id),
            'published_at' => now()->toIso8601String(),
        ];
    }
}
