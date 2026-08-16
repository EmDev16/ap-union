<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;

class PostPolicy
{
    /**
     * Admins supervise the platform, they write questions instead of posts.
     */
    public function create(User $user): bool
    {
        return ! $user->isAdmin();
    }

    public function view(?User $user, Post $post): bool
    {
        if (! $post->isUnderReview()) {
            return true;
        }

        return $user !== null && ($user->isAdmin() || $post->user_id === $user->id);
    }

    public function review(User $user, ?Post $post = null): bool
    {
        return $user->isAdmin();
    }

    public function comment(User $user): bool
    {
        return ! $user->isAdmin();
    }

    public function delete(User $user, Post $post): bool
    {
        return $post->user_id === $user->id;
    }
}
