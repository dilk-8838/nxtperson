<?php

namespace App\Notifications;

use App\Models\ProduceLot;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class FarmerLotStatusUpdatedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public ProduceLot $lot,
        public string $updateType,
        public ?string $detailMessage = null
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

        $titles = [
            'grade_overridden' => "Quality Grade Verified: {$cropName} (Lot #{$this->lot->lot_number})",
            'procurement_approved' => "Procurement Price Approved: {$cropName} (Lot #{$this->lot->lot_number})",
            'purchase_confirmed' => "Purchase Confirmed: {$cropName} (Lot #{$this->lot->lot_number})",
            'published' => "Produce Live on Marketplace: {$cropName} (Lot #{$this->lot->lot_number})",
            'rejected' => "Listing Update: {$cropName} (Lot #{$this->lot->lot_number})",
        ];

        $defaultMessages = [
            'grade_overridden' => "An administrator verified your produce grade as {$this->lot->approved_grade}.",
            'procurement_approved' => "Procurement rate approved at ₹{$this->lot->approved_procurement_price_per_kg}/kg.",
            'purchase_confirmed' => 'Aggregation purchase confirmed for '.number_format((float) $this->lot->confirmed_quantity_kg, 1).' kg.',
            'published' => 'Your produce is now published and purchasable by verified buyers on the marketplace.',
            'rejected' => "Your produce submission was rejected: {$this->lot->rejection_reason}",
        ];

        $title = $titles[$this->updateType] ?? "Produce Lot Status Updated: #{$this->lot->lot_number}";
        $message = $this->detailMessage ?? ($defaultMessages[$this->updateType] ?? "Status changed to {$this->lot->getStatusLabel()}.");

        return [
            'type' => 'farmer_lot_status_updated',
            'update_subtype' => $this->updateType,
            'lot_id' => $this->lot->id,
            'lot_number' => $this->lot->lot_number,
            'crop_name' => $cropName,
            'status' => $this->lot->status,
            'status_label' => $this->lot->getStatusLabel(),
            'title' => $title,
            'message' => $message,
            'view_url' => route('farmer.lots.show', $this->lot->id),
            'action_label' => 'View Produce Status',
            'created_at' => now()->toIso8601String(),
        ];
    }
}
