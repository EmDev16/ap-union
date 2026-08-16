<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewFollower extends Notification
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
            'title' => 'New follower',
            'description' => $this->actorName().' started following you.',
            'url' => route('profile.show', $this->actor),
        ];
    }

    private function actorName(): string
    {
        return $this->actor->username ?: $this->actor->name;
    }
}
