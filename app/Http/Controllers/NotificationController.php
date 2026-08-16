<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    /**
     * How far back the notification history goes.
     */
    public const HISTORY_MONTHS = 3;

    public function index(Request $request): View
    {
        return view('notifications.index', [
            'notifications' => $request->user()->notifications()
                ->where('created_at', '>=', now()->subMonths(self::HISTORY_MONTHS))
                ->get(),
            'months' => self::HISTORY_MONTHS,
        ]);
    }

    public function dismiss(Request $request, string $notification): RedirectResponse
    {
        $request->user()->notifications()->whereKey($notification)->update(['read_at' => now()]);

        return back();
    }

    public function destroy(Request $request, string $notification): RedirectResponse
    {
        $request->user()->notifications()->whereKey($notification)->delete();

        return back();
    }
}
