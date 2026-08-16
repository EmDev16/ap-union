<?php

namespace App\Notifications;

use App\Models\Conversation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PostAppealAnswered extends Notification
{
    use Queueable;

    public function __construct(public Conversation $conversation, public string $summary) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Answer about your post',
            'description' => $this->summary,
            'url' => route('messages.show', $this->conversation),
        ];
    }
}
