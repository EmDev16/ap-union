<?php

namespace App\Http\Controllers;

use App\Models\Prompt;
use App\Models\PromptSubmission;
use Illuminate\Http\Request;

class PromptSubmissionController extends Controller
{
    public function store(Request $request, Prompt $prompt)
    {
        $request->validate([
            'content' => 'nullable|string',
            'media'   => 'nullable|file|mimes:jpg,png,mp4,webm|max:10240',
        ]);

        $submission = PromptSubmission::create([
            'prompt_id' => $prompt->id,
            'user_id'   => auth()->id(),
            'content'   => $request->content,
            'published' => false,
        ]);

        if ($request->hasFile('media')) {
            $submission->media_path = $request
                ->file('media')
                ->store('submissions', 'public');
        }

        // 📅 random publish moment (binnen 7 dagen, 09–20u)
        $submission->scheduled_publish_at = now()
            ->addDays(rand(0, 6))
            ->setTime(rand(9, 20), 0);

        $submission->save();

        return back()->with('success', 'Inzending ontvangen!');
    }
}
