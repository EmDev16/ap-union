<?php

namespace App\Services;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\Post;
use App\Models\User;

/**
 * Keeps the conversation between an admin and a member in sync with a review.
 */
class ReviewConversation
{
    public function between(User $admin, User $member): Conversation
    {
        $conversation = $admin->conversations()
            ->whereHas('participants', fn ($query) => $query->whereKey($member->id))
            ->first();

        if ($conversation) {
            return $conversation;
        }

        $conversation = Conversation::create();
        $conversation->participants()->attach([$admin->id, $member->id]);

        return $conversation;
    }

    public function message(Conversation $conversation, User $sender, string $body, string $systemType, ?Post $post = null): Message
    {
        $message = $conversation->messages()->create([
            'user_id' => $sender->id,
            'system_type' => $systemType,
            'post_id' => $post?->id,
            'body' => $body,
        ]);

        $conversation->update(['last_message_at' => now()]);
        $conversation->participants()->updateExistingPivot($sender->id, ['last_read_at' => now()]);

        return $message;
    }
}
