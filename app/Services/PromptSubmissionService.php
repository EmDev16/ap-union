<?php

namespace App\Services;

use App\Models\PromptSubmission;
use App\Models\Post;

class PromptSubmissionService
{
    public function publishSubmission(PromptSubmission $submission): Post
    {
        $submission->update(['published' => true]);

        return Post::create([
            'user_id' => $submission->user_id,
            'type' => $this->determinePostType($submission),
            'content' => $submission->content,
            'media_path' => $submission->media_path,
            'published_at' => now(),
        ]);
    }

    private function determinePostType(PromptSubmission $submission): string
    {
        if ($submission->media_path) {
            return 'photo';
        }
        return 'text';
    }
}
