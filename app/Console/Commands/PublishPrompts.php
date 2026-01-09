<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PromptSubmission;
use App\Models\Post;

class PublishPrompts extends Command
{
    protected $signature = 'prompts:publish';
    protected $description = 'Publiceer geplande prompt submissions';

    public function handle()
    {
        $subs = PromptSubmission::where('published', false)
            ->whereNotNull('scheduled_publish_at')
            ->where('scheduled_publish_at', '<=', now())
            ->get();

        foreach ($subs as $s) {
            Post::create([
                'user_id'     => $s->user_id,
                'type'        => $s->media_path ? 'photo' : 'text',
                'content'     => $s->content,
                'media_path'  => $s->media_path,
                'published_at'=> now(),
            ]);

            $s->published = true;
            $s->save();
        }

        $this->info("{$subs->count()} submissions gepubliceerd.");
    }
}
