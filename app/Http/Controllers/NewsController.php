<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\View\View;

class NewsController extends Controller
{
    public function index(): View
    {
        return view('news.index', [
            'newsItems' => News::with('user')->where('published_at', '<=', now())->latest('published_at')->get(),
        ]);
    }

    public function show(News $news): View
    {
        abort_if($news->published_at->isFuture(), 404);

        return view('news.show', compact('news'));
    }
}
