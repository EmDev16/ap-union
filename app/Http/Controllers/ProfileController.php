<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Publiek profiel (laatste 3 posts)
     */
    public function show(User $user): View
    {
        $posts = Post::where('user_id', $user->id)
            ->whereNotNull('published_at')
            ->orderBy('published_at', 'desc')
            ->take(3)
            ->get();

        return view('profile.show', compact('user', 'posts'));
    }

    /**
     * Profiel bewerken (ingelogde gebruiker)
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Profiel updaten
     */
    public function update(Request $request): RedirectResponse
    {
        $user = auth()->user();

        $data = $request->validate([
            'username' => 'nullable|string|max:50|unique:profiles,username,' . $user->profile->id,
            'birthday' => 'nullable|date',
            'bio'      => 'nullable|string|max:1000',
            'avatar'   => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->profile->avatar_path = $path;
        }

        $user->profile->fill($data);
        $user->profile->save();

        return redirect()
            ->route('profile.edit')
            ->with('success', 'Profiel bijgewerkt');
    }

    /**
     * Account verwijderen
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
