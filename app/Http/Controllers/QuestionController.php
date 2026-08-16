<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QuestionController extends Controller
{
    /**
     * How far back open questions are shown.
     */
    public const OPEN_MONTHS = 6;

    /**
     * Questions of the last months that the user has not answered yet.
     *
     * @return Builder<Question>
     */
    public static function openFor(User $user): Builder
    {
        return Question::with('user')
            ->where('created_at', '>=', now()->subMonths(self::OPEN_MONTHS))
            ->whereDoesntHave('answers', fn (Builder $query) => $query->where('user_id', $user->id))
            ->orderBy('created_at', 'desc');
    }

    public function index(Request $request): View
    {
        return view('questions.index', [
            'questions' => self::openFor($request->user())->get(),
            'months' => self::OPEN_MONTHS,
        ]);
    }

    public function show(Request $request, Question $question): View
    {
        return view('questions.show', [
            'question' => $question->load('user'),
            'answer' => $question->answerBy($request->user()),
        ]);
    }
}
