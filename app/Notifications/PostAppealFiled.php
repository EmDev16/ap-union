<?php

namespace App\Notifications;

use App\Models\PostAppeal;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PostAppealFiled extends Notification
{
    use Queueable;

    public function __construct(public PostAppeal $appeal) {}

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
            'title' => $this->appeal->stage === PostAppeal::AFTER_DELETE
                ? 'Last appeal to review'
                : 'A member contests a review',
            'description' => 'Write your motivation for this post before you decide.',
            'url' => route('admin.appeals.index').'#appeal-'.$this->appeal->id,
        ];
    }
}
