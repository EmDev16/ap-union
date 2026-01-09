<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prompt extends Model
{
    protected $fillable = [
        'title',
        'prompt_text',
        'allowed_media',
        'published_at',
    ];

    protected $casts = [
        'allowed_media' => 'json',
        'published_at' => 'datetime',
    ];

    public function submissions()
    {
        return $this->hasMany(PromptSubmission::class);
    }
}
