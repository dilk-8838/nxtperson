<?php

namespace App\Notifications;

use App\Models\FpoMemberAggregation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class FpoProduceAggregatedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public FpoMemberAggregation $aggregation
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $fpoName = $this->aggregation->fpoOrganization->name;
        $lot = $this->aggregation->produceLot;
        $cropName = $lot && $lot->crop ? $lot->crop->en_name : ($lot ? ucfirst($lot->crop_slug) : 'Produce');

        return [
            'type' => 'fpo_produce_aggregated',
            'aggregation_id' => $this->aggregation->id,
            'lot_id' => $lot?->id,
            'title' => "Produce Committed to Collective Sale ({$cropName})",
            'message' => "{$fpoName} pooled ".number_format((float) $this->aggregation->committed_quantity_kg, 1)." kg of {$cropName} (Lot #{$lot?->lot_number}) for bulk marketing.",
            'view_url' => $lot ? route('farmer.lots.show', $lot->id) : route('farmer.lots.index'),
            'action_label' => 'View Produce Lot',
            'created_at' => now()->toIso8601String(),
        ];
    }
}
