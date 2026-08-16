<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\Post;
use App\Models\PostAppeal;
use App\Notifications\PostAppealAnswered;
use App\Services\ReviewConversation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PostAppealManagementController extends Controller
{
    public function __construct(private ReviewConversation $conversations) {}

    public function index(Request $request): View
    {
        $this->authorize('review', Post::class);

        return view('admin.appeals.index', [
            'appeals' => PostAppeal::with('post.user', 'user', 'post.appeals.admin')
                ->whereNull('resolved_at')
                ->where('admin_id', $request->user()->id)
                ->orderBy('created_at')
                ->get(),
            'handled' => PostAppeal::with('post', 'user')
                ->whereNotNull('resolved_at')
                ->where('admin_id', $request->user()->id)
                ->latest('resolved_at')
                ->limit(10)
                ->get(),
        ]);
    }

    /**
     * The admin motivates the decision and, from the second appeal on, decides.
     */
    public function update(Request $request, PostAppeal $appeal): RedirectResponse
    {
        $this->authorize('review', $appeal->post);

        abort_unless($appeal->admin_id === $request->user()->id, 403);
        abort_unless($appeal->isOpen(), 403);

        $validated = $request->validate([
            'response' => ['required', 'string', 'max:2000'],
            'decision' => [
                $appeal->stage === PostAppeal::CONTEST ? 'nullable' : 'required',
                'string',
                $appeal->stage === PostAppeal::SECOND
                    ? 'in:'.PostAppeal::REMOVED.','.PostAppeal::RESTORED
                    : 'in:'.PostAppeal::RESTORED.','.PostAppeal::UPHELD,
            ],
        ]);

        $decision = $appeal->stage === PostAppeal::CONTEST
            ? PostAppeal::EXPLAINED
            : $validated['decision'];

        $this->apply($appeal->post, $decision, $request->user()->id);

        $appeal->update([
            'response' => $validated['response'],
            'decision' => $decision,
            'resolved_at' => now(),
        ]);

        $conversation = $this->conversations->between($request->user(), $appeal->user);

        $this->conversations->message(
            $conversation,
            $request->user(),
            $this->bodyFor($decision)."\n\n".$validated['response'],
            $this->systemTypeFor($decision),
            $appeal->post
        );

        $appeal->user->notify(new PostAppealAnswered($conversation, $this->summaryFor($decision)));

        return back()->with('status', 'Je motivatie is verstuurd.');
    }

    private function apply(Post $post, string $decision, int $adminId): void
    {
        if ($decision === PostAppeal::REMOVED) {
            $post->update(['removed_at' => now(), 'removed_by' => $adminId]);
        }

        if ($decision === PostAppeal::RESTORED) {
            $post->update([
                'under_review_at' => null,
                'reviewed_by' => null,
                'review_reason' => null,
                'removed_at' => null,
                'removed_by' => null,
            ]);
        }
    }

    private function systemTypeFor(string $decision): string
    {
        return match ($decision) {
            PostAppeal::EXPLAINED => Message::REVIEW_EXPLAINED,
            PostAppeal::REMOVED => Message::REVIEW_REMOVED,
            default => Message::REVIEW_CLOSED,
        };
    }

    private function bodyFor(string $decision): string
    {
        return match ($decision) {
            PostAppeal::EXPLAINED => 'Here is why your post was flagged. Let us know whether you accept this or still contest it.',
            PostAppeal::REMOVED => 'After a second look your post stays against the rules and has been removed. You can ask one last review by another admin.',
            PostAppeal::RESTORED => 'After a second look your post is online again.',
            default => 'Another admin looked at your post one last time and the removal stands. This closes the review.',
        };
    }

    private function summaryFor(string $decision): string
    {
        return match ($decision) {
            PostAppeal::EXPLAINED => 'An admin explained why your post was flagged.',
            PostAppeal::REMOVED => 'Your post has been removed after a second review.',
            PostAppeal::RESTORED => 'Your post is online again.',
            default => 'The removal of your post stands after a last review.',
        };
    }
}
