<?php

namespace App\Notifications;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewMessage extends Notification
{
    use Queueable;

    public function __construct(public User $actor, public Conversation $conversation) {}

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
            'title' => 'New message',
            'description' => $this->actorName().' sent you a message.',
            'url' => route('messages.show', $this->conversation),
        ];
    }

    private function actorName(): string
    {
        return $this->actor->username ?: $this->actor->name;
    }
}
