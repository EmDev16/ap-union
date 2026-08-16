<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class FollowRequested extends Notification
{
    use Queueable;

    public function __construct(public User $actor) {}

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
            'title' => 'New follow request',
            'description' => ($this->actor->username ?: $this->actor->name).' wants to follow you.',
            'url' => route('follows.requests'),
        ];
    }
}
