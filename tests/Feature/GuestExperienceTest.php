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

test('the home page shows the real feed to signed in users', function () {
    $user = User::factory()->create();
    $followed = User::factory()->create();
    $user->following()->attach($followed->id, ['accepted_at' => now()]);
    Post::factory()->create(['user_id' => $followed->id, 'content' => 'Post van iemand die ik volg']);

    $this->actingAs($user)->get('/')
        ->assertOk()
        ->assertSee('Post van iemand die ik volg')
        ->assertDontSee('Post Title 1 / Following A');
});

test('the home page asks signed in users without follows to explore', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get('/')
        ->assertOk()
        ->assertSee("You don't follow anyone yet", false)
        ->assertSee(route('explore'));
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

test('the search page lists no members without a search term', function () {
    User::factory()->create(['username' => 'alice']);

    $this->get(route('search'))
        ->assertOk()
        ->assertDontSee('alice');
});

test('searching needs at least three characters', function () {
    User::factory()->create(['username' => 'alice']);

    $this->get(route('search', ['q' => 'al']))
        ->assertOk()
        ->assertDontSee('alice');

    $this->getJson(route('search.suggestions', ['q' => 'al']))
        ->assertOk()
        ->assertExactJson([]);
});

test('member suggestions are returned from three characters', function () {
    $alice = User::factory()->create(['name' => 'Alice Example', 'username' => 'alice']);
    User::factory()->create(['name' => 'Bob Example', 'username' => 'bob']);

    $this->getJson(route('search.suggestions', ['q' => 'ali']))
        ->assertOk()
        ->assertExactJson([[
            'name' => 'alice',
            'url' => route('profile.show', $alice),
        ]]);
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

test('an accepted follower sees the full profile', function () {
    $owner = User::factory()->create(['username' => 'publicname']);
    Post::factory()->create(['user_id' => $owner->id, 'content' => 'Zichtbare post']);
    $viewer = User::factory()->create();
    $viewer->following()->attach($owner->id, ['accepted_at' => now()]);

    $this->actingAs($viewer)->get(route('profile.show', $owner))
        ->assertOk()
        ->assertSee('Zichtbare post')
        ->assertSee('Volgers')
        ->assertDontSee('This account is private');
});
