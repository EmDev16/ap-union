<?php

use App\Models\Post;
use App\Models\User;

test('guests cannot follow a user', function () {
    $user = User::factory()->create();

    $this->post(route('users.follow', $user))->assertRedirect(route('login'));
});

test('users can follow and unfollow another user', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();

    $this->actingAs($user)->post(route('users.follow', $other));
    expect($user->following()->pluck('users.id'))->toContain($other->id);

    $this->actingAs($user)->delete(route('users.unfollow', $other));
    expect($user->following()->count())->toBe(0);
});

test('users cannot follow themselves', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->post(route('users.follow', $user));

    expect($user->following()->count())->toBe(0);
});

test('the feed only shows posts of followed users in chronological order', function () {
    $user = User::factory()->create();
    $followed = User::factory()->create();
    $stranger = User::factory()->create();

    $user->following()->attach($followed->id);

    $older = Post::factory()->create(['user_id' => $followed->id, 'content' => 'Oudere post', 'created_at' => now()->subDay()]);
    $newer = Post::factory()->create(['user_id' => $followed->id, 'content' => 'Nieuwere post', 'created_at' => now()]);
    Post::factory()->create(['user_id' => $stranger->id, 'content' => 'Post van een vreemde']);

    $response = $this->actingAs($user)->get(route('home'))->assertOk();

    $posts = $response->viewData('posts');
    expect($posts->pluck('id')->all())->toBe([$newer->id, $older->id]);
    $response->assertDontSee('Post van een vreemde');
});

test('the explore page shows posts of everyone', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create(['content' => 'Post van iemand anders']);

    $this->actingAs($user)->get(route('explore'))->assertOk()->assertSee('Post van iemand anders');
});
