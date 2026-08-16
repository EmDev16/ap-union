<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'username', 'birthday', 'profile_photo', 'about_me', 'is_admin'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public function news(): HasMany
    {
        return $this->hasMany(News::class);
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Members whose follow request this user accepted.
     */
    public function followers(): BelongsToMany
    {
        return $this->allFollowers()->wherePivotNotNull('accepted_at');
    }

    /**
     * Members who accepted the follow request of this user.
     */
    public function following(): BelongsToMany
    {
        return $this->allFollowing()->wherePivotNotNull('accepted_at');
    }

    public function allFollowers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'follows', 'following_id', 'follower_id')
            ->withPivot('accepted_at')
            ->withTimestamps();
    }

    public function allFollowing(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'follows', 'follower_id', 'following_id')
            ->withPivot('accepted_at')
            ->withTimestamps();
    }

    /**
     * Follow requests this user still has to accept or decline.
     */
    public function followRequests(): BelongsToMany
    {
        return $this->allFollowers()->wherePivotNull('accepted_at');
    }

    /**
     * Follow requests this user sent that are still waiting.
     */
    public function pendingFollowing(): BelongsToMany
    {
        return $this->allFollowing()->wherePivotNull('accepted_at');
    }

    public function interests(): BelongsToMany
    {
        return $this->belongsToMany(Interest::class)->orderBy('name');
    }

    /**
     * The maximum number of interests a member can pick.
     */
    public const MAX_INTERESTS = 6;

    /**
     * The maximum number of posts a member can show to visitors.
     */
    public const MAX_SHOWCASED_POSTS = 3;

    public function follows(User $other): bool
    {
        return $this->following()->whereKey($other->id)->exists();
    }

    public function hasPendingRequestFor(User $other): bool
    {
        return $this->pendingFollowing()->whereKey($other->id)->exists();
    }

    /**
     * Profiles are private: only the member, an admin and accepted followers see every post.
     */
    public function showsEveryPostTo(?User $viewer): bool
    {
        if ($viewer === null) {
            return false;
        }

        return $viewer->is($this) || $viewer->isAdmin() || $viewer->follows($this);
    }

    public function likes(): HasMany
    {
        return $this->hasMany(Like::class);
    }

    public function conversations(): BelongsToMany
    {
        return $this->belongsToMany(Conversation::class)->withPivot(['last_read_at', 'cleared_at'])->withTimestamps();
    }

    /**
     * How many conversations have messages this user has not read yet.
     */
    public function unreadConversationCount(): int
    {
        return $this->conversations()
            ->with(['participants', 'messages'])
            ->get()
            ->filter(fn (Conversation $conversation) => $conversation->unreadCountFor($this) > 0)
            ->count();
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function isAdmin(): bool
    {
        return (bool) $this->is_admin;
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'birthday' => 'date',
            'is_admin' => 'boolean',
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
