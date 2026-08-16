<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\User;
use App\Notifications\NewMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class MessageController extends Controller
{
    public function index(Request $request): View
    {
        if (! $request->user()) {
            return view('messages.index', [
                'conversations' => collect(),
                'conversation' => null,
                'filter' => 'all',
            ]);
        }

        return view('messages.index', [
            'conversations' => $this->conversations($request),
            'conversation' => null,
            'filter' => $this->filter($request),
        ]);
    }

    public function show(Request $request, Conversation $conversation): View
    {
        $this->authorizeParticipant($request->user(), $conversation);

        $conversation->load(['messages.user', 'messages.replyTo.user', 'participants']);
        $conversation->participants()->updateExistingPivot($request->user()->id, ['last_read_at' => now()]);

        return view('messages.index', [
            'conversations' => $this->conversations($request),
            'conversation' => $conversation,
            'filter' => $this->filter($request),
        ]);
    }

    public function create(Request $request): View
    {
        return view('messages.create', [
            'members' => $request->user()->following()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
        ]);

        $partner = User::findOrFail($validated['user_id']);

        if (! $request->user()->following()->where('users.id', $partner->id)->exists()) {
            throw ValidationException::withMessages([
                'user_id' => 'You can only start a conversation with someone you follow.',
            ]);
        }

        return to_route('messages.show', $this->conversationWith($request->user(), $partner));
    }

    public function storeMessage(Request $request, Conversation $conversation): RedirectResponse
    {
        $this->authorizeParticipant($request->user(), $conversation);

        $validated = $request->validate([
            'body' => ['nullable', 'string', 'max:5000'],
            'image' => ['nullable', 'image', 'max:5120'],
            'reply_to_id' => ['nullable', 'integer', 'exists:messages,id'],
        ]);

        if (blank($validated['body'] ?? null) && ! $request->hasFile('image')) {
            throw ValidationException::withMessages([
                'body' => 'Write a message or add a picture.',
            ]);
        }

        $replyTo = isset($validated['reply_to_id'])
            ? $conversation->messages()->whereKey($validated['reply_to_id'])->first()
            : null;

        $conversation->messages()->create([
            'user_id' => $request->user()->id,
            'body' => $validated['body'] ?? null,
            'image_path' => $request->hasFile('image')
                ? $request->file('image')->store('messages', 'public')
                : null,
            'reply_to_id' => $replyTo?->id,
        ]);

        $conversation->update(['last_message_at' => now()]);
        $conversation->participants()->updateExistingPivot($request->user()->id, ['last_read_at' => now()]);

        $partner = $conversation->partnerFor($request->user());
        $partner?->notify(new NewMessage($request->user(), $conversation));

        return to_route('messages.show', $conversation);
    }

    /**
     * Hide the conversation for this user only, so it starts clean next time.
     */
    public function destroy(Request $request, Conversation $conversation): RedirectResponse
    {
        $this->authorizeParticipant($request->user(), $conversation);

        $conversation->participants()->updateExistingPivot($request->user()->id, [
            'cleared_at' => now(),
            'last_read_at' => now(),
        ]);

        return to_route('messages');
    }

    private function conversationWith(User $user, User $partner): Conversation
    {
        $conversation = $user->conversations()
            ->whereHas('participants', fn ($query) => $query->whereKey($partner->id))
            ->first();

        if ($conversation) {
            return $conversation;
        }

        $conversation = Conversation::create();
        $conversation->participants()->attach([$user->id, $partner->id]);

        return $conversation;
    }

    /**
     * @return Collection<int, Conversation>
     */
    private function conversations(Request $request)
    {
        $user = $request->user();

        $conversations = $user->conversations()
            ->with(['participants', 'messages'])
            ->orderByRaw('coalesce(last_message_at, conversations.created_at) desc')
            ->get();

        $conversations = $conversations->filter(fn (Conversation $conversation) => $conversation->isVisibleFor($user));

        if ($this->filter($request) === 'unread') {
            $conversations = $conversations->filter(fn (Conversation $conversation) => $conversation->unreadCountFor($user) > 0);
        }

        return $conversations->values();
    }

    private function filter(Request $request): string
    {
        return $request->query('filter') === 'unread' ? 'unread' : 'all';
    }

    private function authorizeParticipant(?User $user, Conversation $conversation): void
    {
        abort_unless($user && $conversation->participants()->whereKey($user->id)->exists(), 403);
    }
}
