<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Question;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AnswerController extends Controller
{
    public function index(Request $request): View
    {
        return view('answers.index', [
            'answers' => $request->user()->answers()
                ->with('question')
                ->orderBy('updated_at', 'desc')
                ->get(),
        ]);
    }

    public function store(Request $request, Question $question): RedirectResponse
    {
        $validated = $request->validate([
            'body' => ['required', 'string', 'max:5000'],
        ]);

        $question->answers()->updateOrCreate(
            ['user_id' => $request->user()->id],
            ['body' => $validated['body']],
        );

        return to_route('answers.index')->with('status', 'Your answer has been saved.');
    }

    public function edit(Request $request, Answer $answer): View
    {
        $this->authorizeOwner($request, $answer);

        return view('answers.edit', [
            'answer' => $answer->load('question'),
        ]);
    }

    public function update(Request $request, Answer $answer): RedirectResponse
    {
        $this->authorizeOwner($request, $answer);

        $validated = $request->validate([
            'body' => ['required', 'string', 'max:5000'],
        ]);

        $answer->update($validated);

        return to_route('answers.index')->with('status', 'Your answer has been updated.');
    }

    private function authorizeOwner(Request $request, Answer $answer): void
    {
        abort_unless($answer->user_id === $request->user()->id, 403);
    }
}
