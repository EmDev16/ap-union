<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Like;

class LikeController extends Controller
{
    public function store(Post $post)
    {
        $user = auth()->user();

        if (!$post->likes()->where('user_id', $user->id)->exists()) {
            $post->likes()->create(['user_id' => $user->id]);
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
