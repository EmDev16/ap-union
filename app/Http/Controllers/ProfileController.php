<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Answer;
use App\Models\Interest;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user()->load('interests'),
            'interests' => Interest::orderBy('category')->orderBy('name')->get()->groupBy('category'),
            'maxInterests' => User::MAX_INTERESTS,
        ]);
    }

    /**
     * Display the user's public profile.
     */
    public function show(User $user): View
    {
        $user->load('interests')->loadCount('posts');

        return view('profile.show', [
            'user' => $user,
            'posts' => $this->visiblePosts($user, auth()->user()),
            'showsEveryPost' => $user->showsEveryPostTo(auth()->user()),
            'answers' => $this->publishedAnswers($user),
            'questions' => $user->isAdmin()
                ? $user->questions()->withCount('answers')->latest()->get()
                : collect(),
        ]);
    }

    /**
     * Everybody sees the chosen posts, only the member, admins and accepted followers see them all.
     *
     * @return Collection<int, Post>
     */
    private function visiblePosts(User $user, ?User $viewer): Collection
    {
        return $user->posts()
            ->with('media', 'comments.user', 'comments.replies', 'likes')
            ->orderBy('created_at', 'desc')
            ->published()
            ->unless($user->showsEveryPostTo($viewer), fn ($query) => $query->where('is_showcased', true))
            ->get();
    }

    /**
     * Answers the admin has published are shown on the profile like a post.
     *
     * @return Collection<int, Answer>
     */
    private function publishedAnswers(User $user): Collection
    {
        return $user->answers()
            ->with('question')
            ->whereHas('question', fn (Builder $question) => $question->whereDate('answers_publish_on', '<=', now()))
            ->orderBy('updated_at', 'desc')
            ->get();
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $interests = $validated['interests'] ?? [];
        unset($validated['profile_photo'], $validated['interests']);

        $request->user()->fill($validated);

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        if ($request->hasFile('profile_photo')) {
            if ($request->user()->profile_photo) {
                Storage::disk('public')->delete($request->user()->profile_photo);
            }

            $request->user()->profile_photo = $request->file('profile_photo')->store('profile-photos', 'public');
        }

        $request->user()->save();
        $request->user()->interests()->sync(array_slice($interests, 0, User::MAX_INTERESTS));

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
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
