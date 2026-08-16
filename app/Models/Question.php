<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'answers_publish_on',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return ['answers_publish_on' => 'date'];
    }

    /**
     * Answers become public on the day the admin picked.
     */
    public function answersArePublic(): bool
    {
        return $this->answers_publish_on !== null && ! $this->answers_publish_on->isFuture();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }

    public function answerBy(User $user): ?Answer
    {
        return $this->answers()->where('user_id', $user->id)->first();
    }
}
