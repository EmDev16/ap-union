<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'content',
        'is_showcased',
        'under_review_at',
        'reviewed_by',
        'review_reason',
        'removed_at',
        'removed_by',
    ];

    /**
     * Posts that are not hidden for review.
     *
     * @param  Builder<Post>  $query
     * @return Builder<Post>
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->whereNull('under_review_at')->whereNull('removed_at');
    }

    public function isUnderReview(): bool
    {
        return $this->under_review_at !== null;
    }

    public function isRemoved(): bool
    {
        return $this->removed_at !== null;
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function media(): HasMany
    {
        return $this->hasMany(PostMedia::class)->orderBy('sort_order');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class)->orderBy('created_at', 'desc');
    }

    public function likes(): HasMany
    {
        return $this->hasMany(Like::class);
    }

    public function appeals(): HasMany
    {
        return $this->hasMany(PostAppeal::class)->orderBy('created_at');
    }

    public function hasOpenAppeal(): bool
    {
        return $this->appeals()->whereNull('resolved_at')->exists();
    }

    /**
     * The appeal the author may still file, or null when there is nothing left to contest.
     */
    public function nextAppealStage(): ?string
    {
        $appeals = $this->appeals()->get();

        if ($appeals->contains(fn (PostAppeal $appeal) => $appeal->isOpen())) {
            return null;
        }

        if ($this->isRemoved()) {
            return $appeals->contains('stage', PostAppeal::AFTER_DELETE) ? null : PostAppeal::AFTER_DELETE;
        }

        if (! $this->isUnderReview() || $appeals->contains('stage', PostAppeal::SECOND)) {
            return null;
        }

        return $appeals->contains('stage', PostAppeal::CONTEST) ? PostAppeal::SECOND : PostAppeal::CONTEST;
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return ['under_review_at' => 'datetime', 'removed_at' => 'datetime', 'is_showcased' => 'boolean'];
    }
}
