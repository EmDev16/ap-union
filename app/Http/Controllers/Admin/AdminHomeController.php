<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\NotificationController;
use App\Models\Contact;
use App\Models\Post;
use App\Models\Question;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminHomeController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Contact::class);

        $since = now()->subMonths(NotificationController::HISTORY_MONTHS);

        return view('admin.home', [
            'contacts' => Contact::latest()->limit(10)->get(),
            'openContacts' => Contact::where('is_answered', false)->count(),
            'stats' => [
                'Leden' => User::where('is_admin', false)->count(),
                'Admins' => User::where('is_admin', true)->count(),
                'Posts' => Post::published()->count(),
                'In review' => Post::whereNotNull('under_review_at')->count(),
                'Vragen' => Question::count(),
                'Open contactberichten' => Contact::where('is_answered', false)->count(),
            ],
            'notifications' => $request->user()->unreadNotifications()
                ->where('created_at', '>=', $since)
                ->limit(5)
                ->get(),
            'notificationCount' => $request->user()->unreadNotifications()
                ->where('created_at', '>=', $since)
                ->count(),
        ]);
    }
}
