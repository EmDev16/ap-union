<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QuestionManagementController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('manage', User::class);

        return view('admin.questions.index', [
            'questions' => $request->user()->questions()->withCount('answers')->latest()->get(),
        ]);
    }

    public function create(): View
    {
        $this->authorize('manage', User::class);

        return view('admin.questions.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('manage', User::class);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'answers_publish_on' => ['nullable', 'date'],
        ]);

        $request->user()->questions()->create($validated);

        return to_route('admin.questions.index')->with('status', 'Vraag toegevoegd.');
    }

    public function edit(Question $question): View
    {
        $this->authorize('manage', User::class);

        return view('admin.questions.edit', ['question' => $question]);
    }

    public function update(Request $request, Question $question): RedirectResponse
    {
        $this->authorize('manage', User::class);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'answers_publish_on' => ['nullable', 'date'],
        ]);

        $question->update($validated);

        return to_route('admin.questions.index')->with('status', 'Vraag aangepast.');
    }
}
