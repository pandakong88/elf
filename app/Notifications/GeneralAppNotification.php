<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class GeneralAppNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $title,
        public string $body,
        public ?string $actionUrl = null,
        public string $category = 'info',
        public array $metadata = []
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
        return [
            'title'      => $this->title,
            'body'       => $this->body,
            'action_url' => $this->actionUrl ?? '/keuangan/billing?tab=transfers',
            'category'   => $this->category,
            'metadata'   => $this->metadata,
            'created_at' => now()->toISOString(),
        ];
    }
}
