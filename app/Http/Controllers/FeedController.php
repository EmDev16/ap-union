<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Question;
use Illuminate\Pagination\LengthAwarePaginator;

class FeedController extends Controller
{
    public function home()
    {
        if (! auth()->check()) {
            return view('welcome');
        }

        return view('welcome', [
            'posts' => $this->followingPosts(),
            'notifications' => auth()->user()->notifications()
                ->where('created_at', '>=', now()->subMonths(NotificationController::HISTORY_MONTHS))
                ->limit(5)
                ->get(),
            'questions' => Question::orderBy('created_at', 'desc')->limit(5)->get(),
        ]);
    }

    public function feed()
    {
        return redirect()->route('home');
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

    /**
     * @return LengthAwarePaginator<int, Post>
     */
    private function followingPosts()
    {
        $followingIds = auth()->user()->following()->pluck('users.id');

        return Post::whereIn('user_id', $followingIds)
            ->with('user', 'media', 'comments.user', 'comments.replies', 'likes')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }
}
