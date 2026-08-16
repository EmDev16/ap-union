<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostAppeal extends Model
{
    use HasFactory;

    protected $fillable = ['post_id', 'user_id', 'admin_id', 'stage', 'reason', 'response', 'decision', 'resolved_at'];

    /**
     * First time the author contests the review.
     */
    public const CONTEST = 'contest';

    /**
     * The author still disagrees after the explanation of the admin.
     */
    public const SECOND = 'second';

    /**
     * Last appeal, after the post was removed, handled by another admin.
     */
    public const AFTER_DELETE = 'after_delete';

    public const EXPLAINED = 'explained';

    public const REMOVED = 'removed';

    public const RESTORED = 'restored';

    public const UPHELD = 'upheld';

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function isOpen(): bool
    {
        return $this->resolved_at === null;
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return ['resolved_at' => 'datetime'];
    }
}
