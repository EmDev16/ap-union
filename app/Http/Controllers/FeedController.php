<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Pagination\LengthAwarePaginator;

class FeedController extends Controller
{
    public function home()
    {
        if (! auth()->check()) {
            return view('welcome');
        }

        if (auth()->user()->isAdmin()) {
            return redirect()->route('admin.home');
        }

        return view('welcome', [
            'posts' => $this->followingPosts(),
            'notifications' => auth()->user()->unreadNotifications()
                ->where('created_at', '>=', now()->subMonths(NotificationController::HISTORY_MONTHS))
                ->limit(5)
                ->get(),
            'notificationCount' => auth()->user()->unreadNotifications()
                ->where('created_at', '>=', now()->subMonths(NotificationController::HISTORY_MONTHS))
                ->count(),
            'questions' => QuestionController::openFor(auth()->user())->limit(5)->get(),
        ]);
    }

    public function feed()
    {
        return redirect()->route('home');
    }

    public function explore()
    {
        $query = Post::published()->with('user', 'media', 'comments.user', 'comments.replies', 'likes');

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

        return Post::published()
            ->whereIn('user_id', $followingIds)
            ->with('user', 'media', 'comments.user', 'comments.replies', 'likes')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }
}
