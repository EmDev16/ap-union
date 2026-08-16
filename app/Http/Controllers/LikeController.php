<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Notifications\PostLiked;

class LikeController extends Controller
{
    public function store(Post $post)
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            return back()->with('error', 'Admins liken geen posts.');
        }

        if (! $post->likes()->where('user_id', $user->id)->exists()) {
            $post->likes()->create(['user_id' => $user->id]);

            if ($post->user_id !== $user->id) {
                $post->user->notify(new PostLiked($user, $post));
            }
        }

        return back();
    }

    public function destroy(Post $post)
    {
        $user = auth()->user();
        $post->likes()->where('user_id', $user->id)->delete();

        return back();
    }
}
