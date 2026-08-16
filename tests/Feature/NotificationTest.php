<?php

use App\Models\Post;
use App\Models\User;
use App\Notifications\NewFollower;
use Illuminate\Support\Str;

test('following a member creates a notification', function () {
    $user = User::factory()->create();
    $partner = User::factory()->create();

    $this->actingAs($user)->post(route('users.follow', $partner));

    expect($partner->notifications()->count())->toBe(1);
});

test('liking and commenting on a post notifies the author', function () {
    $author = User::factory()->create();
    $user = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $author->id]);

    $this->actingAs($user)->post(route('posts.like', $post));
    $this->actingAs($user)->post(route('comments.store', $post), ['content' => 'Mooi bericht']);

    expect($author->notifications()->count())->toBe(2);
});

test('you are not notified about your own post', function () {
    $author = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $author->id]);

    $this->actingAs($author)->post(route('posts.like', $post));

    expect($author->notifications()->count())->toBe(0);
});

test('notifications show on the home page and can be deleted', function () {
    $user = User::factory()->create();
    $actor = User::factory()->create(['username' => 'volger']);
    $user->notify(new NewFollower($actor));
    $notification = $user->notifications()->firstOrFail();

    $this->actingAs($user)->get(route('home'))
        ->assertOk()
        ->assertSee('New follower');

    $this->actingAs($user)->delete(route('notifications.destroy', $notification->id));

    expect($user->notifications()->count())->toBe(0);
});

test('you cannot delete someone elses notification', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();
    $other->notify(new NewFollower($user));
    $notification = $other->notifications()->firstOrFail();

    $this->actingAs($user)->delete(route('notifications.destroy', $notification->id));

    expect($other->notifications()->count())->toBe(1);
});

test('the notifications page only shows the last three months', function () {
    $user = User::factory()->create();
    $actor = User::factory()->create();
    $user->notify(new NewFollower($actor));
    $user->notifications()->firstOrFail()->update(['data' => ['title' => 'Recent notification']]);

    $old = $user->notifications()->firstOrFail()->replicate();
    $old->id = (string) Str::uuid();
    $old->data = ['title' => 'Old notification'];
    $old->created_at = now()->subMonths(4);
    $old->save();

    $this->actingAs($user)->get(route('notifications.index'))
        ->assertOk()
        ->assertSee('Recent notification')
        ->assertDontSee('Old notification');

    $this->actingAs($user)->get(route('home'))
        ->assertOk()
        ->assertSee('Recent notification')
        ->assertDontSee('Old notification');
});
