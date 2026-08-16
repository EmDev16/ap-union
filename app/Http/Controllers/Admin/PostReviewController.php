<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\Post;
use App\Models\User;
use App\Notifications\PostUnderReview;
use App\Services\ReviewConversation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PostReviewController extends Controller
{
    public function __construct(private ReviewConversation $conversations) {}

    public function index(Request $request): View
    {
        $this->authorize('review', Post::class);

        return view('admin.posts.index', [
            'posts' => Post::where(fn ($query) => $query->whereNotNull('under_review_at')->orWhereNotNull('removed_at'))
                ->with('user', 'media', 'reviewer', 'appeals')
                ->latest('under_review_at')
                ->get(),
        ]);
    }

    /**
     * Hide a post for everybody but its author and warn that author.
     */
    public function store(Request $request, Post $post): RedirectResponse
    {
        $this->authorize('review', $post);

        $validated = $request->validate([
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        if ($post->isUnderReview()) {
            return back()->with('status', 'Deze post staat al in review.');
        }

        $post->update([
            'under_review_at' => now(),
            'reviewed_by' => $request->user()->id,
            'review_reason' => $validated['reason'] ?? null,
        ]);

        $this->warnAuthor($request->user(), $post);

        return back()->with('status', 'De post staat in review en is verborgen voor anderen.');
    }

    /**
     * Put a reviewed post back online.
     */
    public function destroy(Request $request, Post $post): RedirectResponse
    {
        $this->authorize('review', $post);

        $post->update([
            'under_review_at' => null,
            'reviewed_by' => null,
            'review_reason' => null,
            'removed_at' => null,
            'removed_by' => null,
        ]);

        return back()->with('status', 'De post staat weer online.');
    }

    /**
     * Take a reviewed post offline, the author can still ask a last review.
     */
    public function remove(Request $request, Post $post): RedirectResponse
    {
        $this->authorize('review', $post);

        if ($post->isRemoved()) {
            return back()->with('status', 'Deze post is al verwijderd.');
        }

        $post->update(['removed_at' => now(), 'removed_by' => $request->user()->id]);

        $conversation = $this->conversations->between($request->user(), $post->user);

        $this->conversations->message(
            $conversation,
            $request->user(),
            'Your post of '.$post->created_at->format('d/m/Y').' is against the rules and has been removed.'
                .' You can ask one last review by another admin.',
            Message::REVIEW_REMOVED,
            $post
        );

        $post->user->notify(new PostUnderReview($request->user(), $post, $conversation));

        return back()->with('status', 'De post is verwijderd.');
    }

    /**
     * Wipe a removed post once no appeal is left.
     */
    public function purge(Request $request, Post $post): RedirectResponse
    {
        $this->authorize('review', $post);

        if (! $post->isRemoved() || $post->hasOpenAppeal() || $post->nextAppealStage() !== null) {
            return back()->with('error', 'Deze post kan nog niet definitief gewist worden.');
        }

        $post->delete();

        return back()->with('status', 'De post is definitief gewist.');
    }

    private function warnAuthor(User $admin, Post $post): void
    {
        $conversation = $this->conversations->between($admin, $post->user);

        $this->conversations->message(
            $conversation,
            $admin,
            'Your post of '.$post->created_at->format('d/m/Y').' is under review'
                .($post->review_reason ? ' ('.$post->review_reason.')' : '')
                .' and is hidden from other members while we check it.'
                .' Let us know whether you agree with the review or want to contest it.',
            Message::REVIEW,
            $post
        );

        $post->user->notify(new PostUnderReview($admin, $post, $conversation));
    }
}
