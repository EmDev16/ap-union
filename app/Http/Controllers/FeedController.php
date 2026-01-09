<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class FeedController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $userIds = $user->followings()
            ->pluck('following_id')
            ->push($user->id);

        $feed = Post::whereIn('user_id', $userIds)
            ->whereNotNull('published_at')
            ->orderBy('published_at', 'desc')
            ->paginate(12);

        return view('feed.index', compact('feed'));
    }
}
