<?php

namespace App\Notifications;

use App\Models\Post;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PostLiked extends Notification
{
    use Queueable;

    public function __construct(public User $actor, public Post $post) {}

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
            'title' => 'New like',
            'description' => $this->actorName().' liked your post.',
            'url' => route('profile.show', $notifiable).'#post-'.$this->post->id,
        ];
    }

    private function actorName(): string
    {
        return $this->actor->username ?: $this->actor->name;
    }
}
