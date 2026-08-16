<?php

namespace App\Notifications;

use App\Models\Conversation;
use App\Models\Post;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PostUnderReview extends Notification
{
    use Queueable;

    public function __construct(public User $actor, public Post $post, public Conversation $conversation) {}

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
            'title' => 'Post under review',
            'description' => 'Your post is under review and hidden from other members. Read the message for the details.',
            'url' => route('messages.show', $this->conversation),
        ];
    }
}
