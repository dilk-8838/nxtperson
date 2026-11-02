<?php

namespace App\Notifications;

use App\Models\FpoOperationalException;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class OperationalExceptionNotification extends Notification
{
    use Queueable;

    public function __construct(
        public FpoOperationalException $exception
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $severityLabel = strtoupper($this->exception->severity);

        return [
            'type' => 'operational_exception_alert',
            'exception_id' => $this->exception->id,
            'exception_number' => $this->exception->exception_number,
            'severity' => $this->exception->severity,
            'title' => "[{$severityLabel}] Exception: {$this->exception->title}",
            'message' => "Exception #{$this->exception->exception_number} flagged: {$this->exception->description}",
            'view_url' => route('fpo.exceptions.index'),
            'action_label' => 'Review Exception',
            'created_at' => now()->toIso8601String(),
        ];
    }
}
