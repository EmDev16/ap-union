<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

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
        $posts = Post::with('user', 'media', 'comments.user', 'comments.replies', 'likes')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('posts.explore', compact('posts'));
    }
}
