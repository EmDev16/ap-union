<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Post;
use App\Models\PostAppeal;
use App\Models\User;
use App\Notifications\PostAppealFiled;
use App\Services\ReviewConversation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PostAppealController extends Controller
{
    public function __construct(private ReviewConversation $conversations) {}

    /**
     * The author contests the review of their post.
     */
    public function store(Request $request, Post $post): RedirectResponse
    {
        $author = $request->user();

        abort_unless($post->user_id === $author->id, 403);

        $stage = $post->nextAppealStage();

        if ($stage === null) {
            return back()->with('error', 'Je kunt deze post nu niet betwisten.');
        }

        $validated = $request->validate([
            'reason' => [$stage === PostAppeal::CONTEST ? 'nullable' : 'required', 'string', 'max:2000'],
        ]);

        $admin = $this->adminFor($post, $stage);

        if ($admin === null) {
            throw ValidationException::withMessages(['reason' => 'Er is geen admin beschikbaar om dit te bekijken.']);
        }

        $appeal = $post->appeals()->create([
            'user_id' => $author->id,
            'admin_id' => $admin->id,
            'stage' => $stage,
            'reason' => $validated['reason'] ?? null,
        ]);

        $conversation = $this->conversations->between($admin, $author);

        $this->conversations->message(
            $conversation,
            $author,
            $this->bodyFor($stage).($validated['reason'] ?? null ? "\n\n".$validated['reason'] : ''),
            Message::REVIEW_CLOSED,
            $post
        );

        $admin->notify(new PostAppealFiled($appeal));

        return back()->with('status', 'Je bericht is bij de admin.');
    }

    /**
     * The last appeal goes to another admin, chosen at random.
     */
    private function adminFor(Post $post, string $stage): ?User
    {
        $handled = $post->appeals()->pluck('admin_id')->push($post->reviewed_by)->filter()->unique();

        if ($stage !== PostAppeal::AFTER_DELETE) {
            return $post->reviewer ?? User::where('is_admin', true)->inRandomOrder()->first();
        }

        return User::where('is_admin', true)->whereKeyNot($handled->all())->inRandomOrder()->first()
            ?? $post->reviewer;
    }

    private function bodyFor(string $stage): string
    {
        return match ($stage) {
            PostAppeal::CONTEST => 'I contest the review of my post and would like to know why it was flagged.',
            PostAppeal::SECOND => 'I still do not agree with the review of my post.',
            default => 'I ask another admin to look at my removed post one last time.',
        };
    }
}
