<?php

namespace App\Notifications;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PostCommented extends Notification
{
    use Queueable;

    public function __construct(public User $actor, public Comment $comment) {}

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
            'title' => 'New comment',
            'description' => $this->actorName().' commented on your post.',
            'url' => route('posts.show', $this->comment->post_id).'#comment-'.$this->comment->id,
        ];
    }

    private function actorName(): string
    {
        return $this->actor->username ?: $this->actor->name;
    }
}
