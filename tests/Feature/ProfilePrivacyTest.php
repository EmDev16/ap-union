<?php

use App\Models\Answer;
use App\Models\Interest;
use App\Models\Post;
use App\Models\Question;
use App\Models\User;

test('the interest catalog is available', function () {
    expect(Interest::count())->toBeGreaterThan(20);
});

test('a member picks at most six interests', function () {
    $user = User::factory()->create();
    $interests = Interest::query()->limit(7)->pluck('id')->all();

    $this->actingAs($user)->patch(route('profile.update'), [
        'name' => $user->name,
        'email' => $user->email,
        'interests' => $interests,
    ])->assertSessionHasErrors('interests');

    expect($user->interests()->count())->toBe(0);
});

test('chosen interests are stored and shown on the profile', function () {
    $user = User::factory()->create();
    $interest = Interest::query()->firstOrFail();

    $this->actingAs($user)->patch(route('profile.update'), [
        'name' => $user->name,
        'email' => $user->email,
        'interests' => [$interest->id],
    ])->assertRedirect();

    expect($user->interests()->pluck('interests.id')->all())->toBe([$interest->id]);

    $this->actingAs($user)->get(route('profile.show', $user))->assertOk()->assertSee($interest->name);
});

test('members are found through their interests', function () {
    $searcher = User::factory()->create();
    $member = User::factory()->create(['username' => 'sterrenkijker']);
    $interest = Interest::query()->firstOrFail();
    $member->interests()->attach($interest->id);

    $this->actingAs($searcher)->get(route('search', ['q' => $interest->name]))
        ->assertOk()
        ->assertSee('sterrenkijker');
});

test('a member chooses at most three visible posts', function () {
    $user = User::factory()->create();
    $posts = Post::factory()->count(4)->create(['user_id' => $user->id]);

    $this->actingAs($user)->patch(route('posts.showcase.update'), [
        'posts' => $posts->pluck('id')->all(),
    ])->assertSessionHasErrors('posts');

    $chosen = $posts->take(3)->pluck('id')->all();

    $this->actingAs($user)->patch(route('posts.showcase.update'), ['posts' => $chosen])
        ->assertRedirect(route('profile.show', $user));

    expect($user->posts()->where('is_showcased', true)->pluck('id')->all())->toBe($chosen);
});

test('a member cannot showcase the post of somebody else', function () {
    $user = User::factory()->create();
    $other = Post::factory()->create();

    $this->actingAs($user)->patch(route('posts.showcase.update'), ['posts' => [$other->id]]);

    expect($other->fresh()->is_showcased)->toBeFalse();
});

test('guests and strangers only see the chosen posts', function () {
    $owner = User::factory()->create(['username' => 'privaat']);
    Post::factory()->create(['user_id' => $owner->id, 'content' => 'Gekozen post', 'is_showcased' => true]);
    Post::factory()->create(['user_id' => $owner->id, 'content' => 'Verborgen post']);
    $stranger = User::factory()->create();

    $this->get(route('profile.show', $owner))
        ->assertOk()
        ->assertSee('Gekozen post')
        ->assertDontSee('Verborgen post');

    $this->actingAs($stranger)->get(route('profile.show', $owner))
        ->assertOk()
        ->assertSee('Gekozen post')
        ->assertDontSee('Verborgen post');
});

test('a pending follower still only sees the chosen posts', function () {
    $owner = User::factory()->create();
    Post::factory()->create(['user_id' => $owner->id, 'content' => 'Verborgen post']);
    $viewer = User::factory()->create();

    $this->actingAs($viewer)->post(route('users.follow', $owner));

    $this->actingAs($viewer)->get(route('profile.show', $owner))
        ->assertOk()
        ->assertSee('This account is private')
        ->assertDontSee('Verborgen post');
});

test('the follower and following lists are shown to members', function () {
    $owner = User::factory()->create(['username' => 'gevolgde']);
    $follower = User::factory()->create(['username' => 'volger']);
    $follower->following()->attach($owner->id, ['accepted_at' => now()]);

    $this->actingAs($follower)->get(route('follows.followers', $owner))->assertOk()->assertSee('volger');
    $this->actingAs($follower)->get(route('follows.following', $follower))->assertOk()->assertSee('gevolgde');
});

test('guests cannot open the follower lists', function () {
    $owner = User::factory()->create();

    $this->get(route('follows.followers', $owner))->assertRedirect(route('login'));
    $this->get(route('follows.following', $owner))->assertRedirect(route('login'));
    $this->get(route('follows.requests'))->assertRedirect(route('login'));
    $this->get(route('posts.showcase'))->assertRedirect(route('login'));
});

test('answers stay hidden until the day the admin picked', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $member = User::factory()->create();
    $question = Question::factory()->create(['user_id' => $admin->id]);
    Answer::factory()->create([
        'user_id' => $member->id,
        'question_id' => $question->id,
        'body' => 'Mijn antwoord op de vraag',
    ]);

    $this->get(route('profile.show', $member))->assertOk()->assertDontSee('Mijn antwoord op de vraag');

    $this->actingAs($admin)->patch(route('admin.questions.update', $question), [
        'title' => $question->title,
        'description' => $question->description,
        'answers_publish_on' => now()->addDay()->toDateString(),
    ])->assertRedirect();

    $this->get(route('profile.show', $member))->assertOk()->assertDontSee('Mijn antwoord op de vraag');

    $this->actingAs($admin)->patch(route('admin.questions.update', $question), [
        'title' => $question->title,
        'description' => $question->description,
        'answers_publish_on' => now()->toDateString(),
    ])->assertRedirect();

    $this->get(route('profile.show', $member))->assertOk()->assertSee('Mijn antwoord op de vraag');
});

test('deleting your account asks for a confirmation', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get(route('profile.edit'))->assertOk()->assertSee('confirm(', false);
});
