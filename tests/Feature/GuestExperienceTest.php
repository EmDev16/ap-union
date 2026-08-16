<?php

use App\Models\Post;
use App\Models\User;

test('the home page invites guests to sign in instead of showing example content', function () {
    $response = $this->get('/');

    $response->assertOk()
        ->assertSee('Welcome to')
        ->assertSee(route('login'))
        ->assertDontSee('Post Title 1 / Following A')
        ->assertDontSee('Notification 1');
});

test('the home page keeps the example content for signed in users', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get('/')
        ->assertOk()
        ->assertSee('Post Title 1 / Following A')
        ->assertSee('Notification 1');
});

test('explore shows guests a maximum of ten posts without pagination', function () {
    Post::factory()->count(15)->create();

    $response = $this->get(route('explore'));

    $response->assertOk()
        ->assertViewHas('posts', fn ($posts) => $posts->count() === 10)
        ->assertDontSee('?page=2')
        ->assertSee(route('login'));
});

test('explore stays paginated for signed in users', function () {
    Post::factory()->count(15)->create();
    $user = User::factory()->create();

    $this->actingAs($user)->get(route('explore'))
        ->assertOk()
        ->assertViewHas('posts', fn ($posts) => $posts->hasPages())
        ->assertSee('page=2');
});

test('the messages page proposes signing in to guests', function () {
    $this->get(route('messages'))
        ->assertOk()
        ->assertSee('Sign in to start a conversation')
        ->assertDontSee('Conversation with User A');
});

test('guests can search for members', function () {
    User::factory()->create(['name' => 'Alice Example', 'username' => 'alice']);
    User::factory()->create(['name' => 'Bob Example', 'username' => 'bob']);

    $this->get(route('search', ['q' => 'alice']))
        ->assertOk()
        ->assertSee('alice')
        ->assertDontSee('bob');
});

test('guests only see username, description and post count on a profile', function () {
    $user = User::factory()->create([
        'name' => 'Hidden Realname',
        'username' => 'publicname',
        'about_me' => 'Mijn beschrijving',
    ]);
    Post::factory()->count(3)->create(['user_id' => $user->id, 'content' => 'Geheime post']);

    $this->get(route('profile.show', $user))
        ->assertOk()
        ->assertSee('publicname')
        ->assertSee('Mijn beschrijving')
        ->assertSee('3')
        ->assertSee('This account is private')
        ->assertDontSee('Hidden Realname')
        ->assertDontSee('Geheime post')
        ->assertDontSee('Volgers');
});

test('signed in users see the full profile', function () {
    $owner = User::factory()->create(['username' => 'publicname']);
    Post::factory()->create(['user_id' => $owner->id, 'content' => 'Zichtbare post']);
    $viewer = User::factory()->create();

    $this->actingAs($viewer)->get(route('profile.show', $owner))
        ->assertOk()
        ->assertSee('Zichtbare post')
        ->assertSee('Volgers')
        ->assertDontSee('This account is private');
});
