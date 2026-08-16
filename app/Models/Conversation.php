<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = ['last_message_at'];

    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withPivot('last_read_at')->withTimestamps();
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

        return $this->messages
            ->where('user_id', '!=', $user->id)
            ->filter(fn (Message $message) => $lastReadAt === null || $message->created_at->greaterThan($lastReadAt))
            ->count();
    }

    protected function casts(): array
    {
        return [
            'last_message_at' => 'datetime',
        ];
    }
}
