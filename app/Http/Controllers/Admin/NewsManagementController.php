<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreNewsRequest;
use App\Http\Requests\UpdateNewsRequest;
use App\Models\News;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class NewsManagementController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', News::class);
        return view('admin.news.index', ['newsItems' => News::with('user')->latest('published_at')->get()]);
    }

    public function create(): View
    {
        $this->authorize('create', News::class);
        return view('admin.news.create');
    }

    public function store(StoreNewsRequest $request): RedirectResponse
    {
        $this->authorize('create', News::class);
        $data = $request->validated();
        $data['image'] = $request->file('image')?->store('news', 'public');
        $data['user_id'] = $request->user()->id;

        News::create($data);

        return to_route('admin.news.index')->with('status', 'Nieuwsbericht aangemaakt.');
    }

    public function edit(News $news): View
    {
        $this->authorize('update', $news);
        return view('admin.news.edit', compact('news'));
    }

    public function update(UpdateNewsRequest $request, News $news): RedirectResponse
    {
        $this->authorize('update', $news);
        $data = $request->validated();

        if ($request->hasFile('image')) {
            if ($news->image) Storage::disk('public')->delete($news->image);
            $data['image'] = $request->file('image')->store('news', 'public');
        }

        $news->update($data);

        return to_route('admin.news.index')->with('status', 'Nieuwsbericht bijgewerkt.');
    }

    public function destroy(News $news): RedirectResponse
    {
        $this->authorize('delete', $news);
        if ($news->image) Storage::disk('public')->delete($news->image);
        $news->delete();

        return to_route('admin.news.index')->with('status', 'Nieuwsbericht verwijderd.');
    }
}
