<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class FollowController extends Controller
{
    public function store(User $user)
    {
        $auth = auth()->user();

        if ($auth->id === $user->id) {
            return back()->with('error', 'Je kunt jezelf niet volgen.');
        }

        if (!$auth->following()->where('following_id', $user->id)->exists()) {
            $auth->following()->attach($user->id);
        }

        return back()->with('success', 'Je volgt nu ' . $user->name);
    }

    public function destroy(User $user)
    {
        $auth = auth()->user();
        $auth->following()->detach($user->id);

        return back()->with('success', 'Je volgt ' . $user->name . ' niet meer.');
    }
}
