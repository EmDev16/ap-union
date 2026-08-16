<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = ['last_message_at'];

    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withPivot(['last_read_at', 'cleared_at'])->withTimestamps();
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function latestMessage(): HasOne
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }

    /**
     * The other participant of a one on one conversation.
     */
    public function partnerFor(User $user): ?User
    {
        return $this->participants->firstWhere('id', '!=', $user->id);
    }

    public function unreadCountFor(User $user): int
    {
        $participant = $this->participants->firstWhere('id', $user->id);
        $lastReadAt = $participant?->pivot->last_read_at;

        return $this->visibleMessagesFor($user)
            ->where('user_id', '!=', $user->id)
            ->filter(fn (Message $message) => $lastReadAt === null || $message->created_at->greaterThan($lastReadAt))
            ->count();
    }

    /**
     * Messages the user can still see, so messages after they cleared the conversation.
     *
     * @return Collection<int, Message>
     */
    public function visibleMessagesFor(User $user): Collection
    {
        $clearedAt = $this->clearedAtFor($user);

        return $this->messages
            ->filter(fn (Message $message) => $clearedAt === null || $message->created_at->greaterThan($clearedAt))
            ->sortBy('created_at')
            ->values();
    }

    public function clearedAtFor(User $user): ?Carbon
    {
        $clearedAt = $this->participants->firstWhere('id', $user->id)?->pivot->cleared_at;

        return $clearedAt === null ? null : Carbon::parse($clearedAt);
    }

    /**
     * A cleared conversation only comes back when there are new messages.
     */
    public function isVisibleFor(User $user): bool
    {
        return $this->clearedAtFor($user) === null || $this->visibleMessagesFor($user)->isNotEmpty();
    }

    protected function casts(): array
    {
        return [
            'last_message_at' => 'datetime',
        ];
    }
}
