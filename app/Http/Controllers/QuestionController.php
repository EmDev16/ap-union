<?php

namespace App\Http\Controllers;

use App\Models\Question;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QuestionController extends Controller
{
    public function index(Request $request): View
    {
        return view('questions.index', [
            'questions' => Question::with('user')->orderBy('created_at', 'desc')->get(),
        ]);
    }

    public function show(Request $request, Question $question): View
    {
        return view('questions.show', [
            'question' => $question->load('user'),
            'answer' => $question->answerBy($request->user()),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $request->user()->questions()->create($validated);

        return to_route('questions.index')->with('status', 'Your question has been posted.');
    }
}
