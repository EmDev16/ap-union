<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromptSubmission extends Model
{
    protected $fillable = [
        'prompt_id',
        'user_id',
        'content',
        'media_path',
        'scheduled_publish_at',
        'published',
    ];

    protected $casts = [
        'scheduled_publish_at' => 'datetime',
        'published' => 'boolean',
    ];

    public function prompt()
    {
        return $this->belongsTo(Prompt::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function post()
    {
        return $this->hasOne(Post::class);
    }
}
