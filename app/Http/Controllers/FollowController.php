<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\NewFollower;

class FollowController extends Controller
{
    public function store(User $user)
    {
        $auth = auth()->user();

        if ($auth->id === $user->id) {
            return back()->with('error', 'Je kunt jezelf niet volgen.');
        }

        if ($auth->isAdmin() !== $user->isAdmin()) {
            return back()->with('error', $auth->isAdmin()
                ? 'Als admin kun je enkel andere admins volgen.'
                : 'Je kunt een adminaccount niet volgen.');
        }

        if (! $auth->following()->where('following_id', $user->id)->exists()) {
            $auth->following()->attach($user->id);
            $user->notify(new NewFollower($auth));
        }

        return back()->with('success', 'Je volgt nu '.$user->name);
    }

    public function destroy(User $user)
    {
        $auth = auth()->user();
        $auth->following()->detach($user->id);

        return back()->with('success', 'Je volgt '.$user->name.' niet meer.');
    }
}
