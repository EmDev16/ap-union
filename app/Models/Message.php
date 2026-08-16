<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'user_id',
        'reply_to_id',
        'body',
        'image_path',
        'system_type',
        'post_id',
    ];

    /**
     * A message the app sent itself, like a post review warning.
     */
    public const REVIEW = 'post_review';

    /**
     * The admin explained why the post was flagged, the author can still contest.
     */
    public const REVIEW_EXPLAINED = 'post_review_explained';

    /**
     * The post was removed, the author can ask a last review by another admin.
     */
    public const REVIEW_REMOVED = 'post_review_removed';

    /**
     * Final outcome of a review, without any further options.
     */
    public const REVIEW_CLOSED = 'post_review_closed';

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function replyTo(): BelongsTo
    {
        return $this->belongsTo(Message::class, 'reply_to_id');
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * The post this review message is about, as long as the author can still react.
     */
    public function reviewPost(): ?Post
    {
        $actionable = [self::REVIEW, self::REVIEW_EXPLAINED, self::REVIEW_REMOVED];

        if (! in_array($this->system_type, $actionable, true) || $this->post === null) {
            return null;
        }

        return $this->post->nextAppealStage() === null ? null : $this->post;
    }
}
