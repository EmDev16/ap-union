<?php

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

function conversationBetween(User $one, User $two): Conversation
{
    $conversation = Conversation::create(['last_message_at' => now()]);
    $conversation->participants()->attach([$one->id, $two->id]);

    return $conversation;
}

test('guests see the sign in invitation on messages', function () {
    $this->get(route('messages'))
        ->assertOk()
        ->assertSee('Log in', false);
});

test('a member can start a conversation with someone they follow', function () {
    $user = User::factory()->create();
    $partner = User::factory()->create();
    $user->following()->attach($partner->id);

    $this->actingAs($user)
        ->post(route('messages.store'), ['user_id' => $partner->id])
        ->assertRedirect();

    expect($user->conversations()->count())->toBe(1);
});

test('a member cannot start a conversation with someone they do not follow', function () {
    $user = User::factory()->create();
    $partner = User::factory()->create();

    $this->actingAs($user)
        ->post(route('messages.store'), ['user_id' => $partner->id])
        ->assertSessionHasErrors('user_id');

    expect($user->conversations()->count())->toBe(0);
});

test('starting a conversation twice reuses the same conversation', function () {
    $user = User::factory()->create();
    $partner = User::factory()->create();
    $user->following()->attach($partner->id);

    $this->actingAs($user)->post(route('messages.store'), ['user_id' => $partner->id]);
    $this->actingAs($user)->post(route('messages.store'), ['user_id' => $partner->id]);

    expect($user->conversations()->count())->toBe(1);
});

test('only participants can read a conversation', function () {
    $user = User::factory()->create();
    $partner = User::factory()->create();
    $stranger = User::factory()->create();
    $conversation = conversationBetween($user, $partner);

    $this->actingAs($user)->get(route('messages.show', $conversation))->assertOk();
    $this->actingAs($stranger)->get(route('messages.show', $conversation))->assertForbidden();
});

test('a member can send a long message', function () {
    $user = User::factory()->create();
    $partner = User::factory()->create();
    $conversation = conversationBetween($user, $partner);
    $body = str_repeat('a', 4999);

    $this->actingAs($user)
        ->post(route('messages.reply', $conversation), ['body' => $body])
        ->assertRedirect(route('messages.show', $conversation));

    expect($conversation->messages()->first()->body)->toBe($body);
});

test('an empty message is rejected', function () {
    $user = User::factory()->create();
    $partner = User::factory()->create();
    $conversation = conversationBetween($user, $partner);

    $this->actingAs($user)
        ->post(route('messages.reply', $conversation), ['body' => ''])
        ->assertSessionHasErrors('body');

    expect($conversation->messages()->count())->toBe(0);
});

test('a member can send a picture', function () {
    Storage::fake('public');
    $user = User::factory()->create();
    $partner = User::factory()->create();
    $conversation = conversationBetween($user, $partner);

    $this->actingAs($user)->post(route('messages.reply', $conversation), [
        'image' => UploadedFile::fake()->image('photo.jpg'),
    ])->assertRedirect();

    $message = $conversation->messages()->first();
    expect($message->image_path)->not->toBeNull();
    Storage::disk('public')->assertExists($message->image_path);

    $this->actingAs($user)->get(route('messages'))
        ->assertOk()
        ->assertSee('Picture')
        ->assertDontSee('No messages yet');
});

test('a member can reply to a specific message', function () {
    $user = User::factory()->create();
    $partner = User::factory()->create();
    $conversation = conversationBetween($user, $partner);
    $original = $conversation->messages()->create(['user_id' => $partner->id, 'body' => 'Hallo']);

    $this->actingAs($user)->post(route('messages.reply', $conversation), [
        'body' => 'Antwoord',
        'reply_to_id' => $original->id,
    ]);

    expect($conversation->messages()->latest('id')->first()->reply_to_id)->toBe($original->id);
});

test('a reply to a message from another conversation is ignored', function () {
    $user = User::factory()->create();
    $partner = User::factory()->create();
    $conversation = conversationBetween($user, $partner);
    $other = conversationBetween($user, User::factory()->create());
    $foreign = $other->messages()->create(['user_id' => $user->id, 'body' => 'Elders']);

    $this->actingAs($user)->post(route('messages.reply', $conversation), [
        'body' => 'Antwoord',
        'reply_to_id' => $foreign->id,
    ]);

    expect($conversation->messages()->latest('id')->first()->reply_to_id)->toBeNull();
});

test('unread messages are counted and can be filtered', function () {
    $user = User::factory()->create();
    $partner = User::factory()->create();
    $conversation = conversationBetween($user, $partner);
    $conversation->messages()->create(['user_id' => $partner->id, 'body' => 'Nieuw bericht']);

    $conversation->load('messages', 'participants');
    expect($conversation->unreadCountFor($user))->toBe(1);

    $this->actingAs($user)->get(route('messages', ['filter' => 'unread']))
        ->assertOk()
        ->assertSee($partner->username ?: $partner->name);

    $this->actingAs($user)->get(route('messages.show', $conversation))->assertOk();

    $conversation->refresh()->load('messages', 'participants');
    expect($conversation->unreadCountFor($user))->toBe(0);
});

test('the new conversation page only lists people you follow', function () {
    $user = User::factory()->create();
    $followed = User::factory()->create(['name' => 'Gevolgde Vriend']);
    User::factory()->create(['name' => 'Onbekende Persoon']);
    $user->following()->attach($followed->id);

    $this->actingAs($user)->get(route('messages.create'))
        ->assertOk()
        ->assertSee('Gevolgde Vriend')
        ->assertDontSee('Onbekende Persoon');
});

test('deleting a conversation only hides it for you', function () {
    $user = User::factory()->create();
    $partner = User::factory()->create();
    $conversation = conversationBetween($user, $partner);
    $conversation->messages()->create(['user_id' => $partner->id, 'body' => 'Oud bericht']);

    $this->actingAs($user)->delete(route('messages.destroy', $conversation))
        ->assertRedirect(route('messages'));

    $this->actingAs($user)->get(route('messages'))
        ->assertOk()
        ->assertDontSee('Oud bericht');

    $this->actingAs($partner)->get(route('messages'))
        ->assertOk()
        ->assertSee('Oud bericht');
});

test('a deleted conversation comes back empty with a new message', function () {
    $user = User::factory()->create();
    $partner = User::factory()->create();
    $conversation = conversationBetween($user, $partner);
    $conversation->messages()->create(['user_id' => $partner->id, 'body' => 'Oud bericht']);

    $this->actingAs($user)->delete(route('messages.destroy', $conversation));

    $this->travel(1)->minute();
    $this->actingAs($partner)->post(route('messages.reply', $conversation), ['body' => 'Nieuw bericht']);

    $this->actingAs($user)->get(route('messages.show', $conversation))
        ->assertOk()
        ->assertSee('Nieuw bericht')
        ->assertDontSee('Oud bericht');
});

test('only a participant can delete a conversation', function () {
    $user = User::factory()->create();
    $conversation = conversationBetween(User::factory()->create(), User::factory()->create());

    $this->actingAs($user)->delete(route('messages.destroy', $conversation))->assertForbidden();
});

test('the navbar shows how many conversations have new messages', function () {
    $user = User::factory()->create();
    $partner = User::factory()->create();
    $conversation = conversationBetween($user, $partner);
    $conversation->messages()->create(['user_id' => $partner->id, 'body' => 'Hallo']);

    $this->actingAs($user)->get(route('home'))
        ->assertOk()
        ->assertSee('nav-count-badge', false);

    expect($user->unreadConversationCount())->toBe(1);
});
