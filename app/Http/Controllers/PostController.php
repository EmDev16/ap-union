<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PostController extends Controller
{
    public function show(Post $post): View
    {
        $this->authorize('view', $post);

        return view('posts.show', [
            'post' => $post->load('user', 'media', 'comments.user', 'comments.replies', 'likes'),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Post::class);

        return view('posts.create');
    }

    public function store(StorePostRequest $request): RedirectResponse
    {
        $this->authorize('create', Post::class);

        $post = $request->user()->posts()->create(['content' => $request->validated('content')]);

        foreach ($request->file('media', []) as $index => $file) {
            $mimeType = $file->getMimeType();
            $type = str_starts_with($mimeType, 'video/') ? 'video' : 'image';
            $post->media()->create([
                'path' => $file->store('posts', 'public'),
                'type' => $type,
                'mime_type' => $mimeType,
                'sort_order' => $index,
            ]);
        }

        return to_route('profile.show', $request->user())->with('status', 'Post gepubliceerd.');
    }

    /**
     * Pick the posts visitors of the private profile may read.
     */
    public function showcase(Request $request): View
    {
        return view('posts.showcase', [
            'posts' => $request->user()->posts()->published()->latest()->get(),
            'maximum' => User::MAX_SHOWCASED_POSTS,
        ]);
    }

    public function updateShowcase(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'posts' => ['nullable', 'array', 'max:'.User::MAX_SHOWCASED_POSTS],
            'posts.*' => ['integer'],
        ]);

        $chosen = $request->user()->posts()
            ->whereIn('id', $validated['posts'] ?? [])
            ->pluck('id');

        $request->user()->posts()->update(['is_showcased' => false]);
        $request->user()->posts()->whereIn('id', $chosen)->update(['is_showcased' => true]);

        return to_route('profile.show', $request->user())->with('status', 'Je gekozen posts zijn bewaard.');
    }

    public function destroy(Post $post): RedirectResponse
    {
        $this->authorize('delete', $post);

        foreach ($post->media as $media) {
            Storage::disk('public')->delete($media->path);
        }

        $post->delete();

        return back()->with('status', 'Post verwijderd.');
    }
}
