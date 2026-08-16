<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use App\Notifications\PostCommented;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Post $post, Request $request)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:500',
            'parent_id' => 'nullable|exists:comments,id',
        ]);

        $comment = $post->comments()->create([
            'user_id' => auth()->id(),
            'content' => $validated['content'],
            'parent_id' => $validated['parent_id'] ?? null,
        ]);

        if ($post->user_id !== auth()->id()) {
            $post->user->notify(new PostCommented(auth()->user(), $comment));
        }

        return back();
    }

    public function destroy(Comment $comment)
    {
        if ($comment->user_id !== auth()->id() && ! auth()->user()->is_admin) {
            return back()->with('error', 'Je kunt dit bericht niet verwijderen.');
        }

        $comment->delete();

        return back()->with('success', 'Opmerking verwijderd.');
    }
}
