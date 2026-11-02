<?php

namespace App\Notifications;

use App\Models\FpoGateExit;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class GateExitAuthorizedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public FpoGateExit $exit
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $movementLabel = ucfirst(str_replace('_', ' ', $this->exit->movement_type));

        return [
            'type' => 'gate_exit_authorized',
            'exit_id' => $this->exit->id,
            'exit_number' => $this->exit->exit_number,
            'movement_type' => $this->exit->movement_type,
            'title' => "Gate Exit Authorized: #{$this->exit->exit_number}",
            'message' => "Movement ({$movementLabel}) of {$this->exit->dispatched_quantity} {$this->exit->unit} for vehicle {$this->exit->vehicle_number} has been authorized by {$this->exit->authorizer_name}.",
            'view_url' => route('fpo.gate-exits.index'),
            'action_label' => 'View Gate Exit Register',
            'created_at' => now()->toIso8601String(),
        ];
    }
}
