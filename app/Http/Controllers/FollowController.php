<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\FollowAccepted;
use App\Notifications\FollowRequested;
use App\Notifications\NewFollower;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FollowController extends Controller
{
    /**
     * Ask a member to follow them, they decide whether to accept.
     */
    public function store(User $user): RedirectResponse
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

        if (! $auth->allFollowing()->whereKey($user->id)->exists()) {
            $auth->allFollowing()->attach($user->id, ['accepted_at' => null]);
            $user->notify(new FollowRequested($auth));
        }

        return back()->with('success', 'Je verzoek is verstuurd naar '.$user->name);
    }

    public function destroy(User $user): RedirectResponse
    {
        auth()->user()->allFollowing()->detach($user->id);

        return back()->with('success', 'Je volgt '.$user->name.' niet meer.');
    }

    /**
     * The follow requests waiting for the member.
     */
    public function requests(Request $request): View
    {
        return view('follows.requests', [
            'requests' => $request->user()->followRequests()->orderBy('name')->get(),
        ]);
    }

    public function accept(Request $request, User $user): RedirectResponse
    {
        $pending = $request->user()->followRequests()->whereKey($user->id)->exists();

        abort_unless($pending, 404);

        $request->user()->allFollowers()->updateExistingPivot($user->id, ['accepted_at' => now()]);

        $user->notify(new FollowAccepted($request->user()));
        $request->user()->notify(new NewFollower($user));

        return back()->with('success', $user->name.' volgt je nu.');
    }

    public function decline(Request $request, User $user): RedirectResponse
    {
        $request->user()->followRequests()->detach($user->id);

        return back()->with('success', 'Verzoek geweigerd.');
    }

    /**
     * The accepted followers of a member.
     */
    public function followers(User $user): View
    {
        return view('follows.index', [
            'user' => $user,
            'title' => 'Volgers van '.($user->username ?: $user->name),
            'members' => $user->followers()->orderBy('name')->get(),
        ]);
    }

    public function following(User $user): View
    {
        return view('follows.index', [
            'user' => $user,
            'title' => ($user->username ?: $user->name).' volgt',
            'members' => $user->following()->orderBy('name')->get(),
        ]);
    }
}
