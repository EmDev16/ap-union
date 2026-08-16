<?php

namespace App\Http\Controllers;

use App\Models\Post;

class FeedController extends Controller
{
    public function feed()
    {
        $followingIds = auth()->user()->following()->pluck('users.id');
        $posts = Post::whereIn('user_id', $followingIds)
            ->with('user', 'media', 'comments.user', 'comments.replies', 'likes')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('dashboard', compact('posts'));
    }

    public function explore()
    {
        $query = Post::with('user', 'media', 'comments.user', 'comments.replies', 'likes');

        if (! auth()->check()) {
            $posts = $query->inRandomOrder()->limit(10)->get();

            return view('posts.explore', compact('posts'));
        }

        $posts = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('posts.explore', compact('posts'));
    }
}
