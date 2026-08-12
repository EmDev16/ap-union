<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PostController extends Controller
{
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
            $type = str_starts_with($file->getMimeType(), 'video/') ? 'video' : 'image';
            $post->media()->create([
                'path' => $file->store('posts', 'public'),
                'type' => $type,
                'sort_order' => $index,
            ]);
        }

        return to_route('profile.show', $request->user())->with('status', 'Post gepubliceerd.');
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
