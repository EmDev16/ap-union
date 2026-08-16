<?php

use App\Models\Answer;
use App\Models\Question;
use App\Models\User;

test('questions are shown on the home page and link to the answer page', function () {
    $user = User::factory()->create();
    $question = Question::factory()->create(['title' => 'Hoe werkt AP Union?']);

    $this->actingAs($user)->get(route('home'))
        ->assertOk()
        ->assertSee('Hoe werkt AP Union?')
        ->assertSee(route('questions.show', $question));
});

test('guests cannot open the question pages', function () {
    $question = Question::factory()->create();

    $this->get(route('questions.show', $question))->assertRedirect(route('login'));
    $this->get(route('answers.index'))->assertRedirect(route('login'));
});

test('the questions page only lists open questions of the last six months', function () {
    $user = User::factory()->create();
    Question::factory()->create(['title' => 'Open vraag']);
    $answered = Question::factory()->create(['title' => 'Beantwoorde vraag']);
    Question::factory()->create([
        'title' => 'Oude vraag',
        'created_at' => now()->subMonths(7),
    ]);

    Answer::factory()->create(['question_id' => $answered->id, 'user_id' => $user->id]);

    $this->actingAs($user)->get(route('questions.index'))
        ->assertOk()
        ->assertSee('Open vraag')
        ->assertDontSee('Beantwoorde vraag')
        ->assertDontSee('Oude vraag');

    $this->actingAs($user)->get(route('home'))
        ->assertOk()
        ->assertDontSee('Beantwoorde vraag')
        ->assertDontSee('Oude vraag');
});

test('a member can answer a question and find it back', function () {
    $user = User::factory()->create();
    $question = Question::factory()->create(['title' => 'Wat lees je nu?']);

    $this->actingAs($user)->post(route('answers.store', $question), [
        'body' => 'Ik lees momenteel een roman.',
    ])->assertRedirect(route('answers.index'));

    $this->actingAs($user)->get(route('answers.index'))
        ->assertOk()
        ->assertSee('Wat lees je nu?')
        ->assertSee('Ik lees momenteel een roman.');
});

test('answering the same question twice updates the existing answer', function () {
    $user = User::factory()->create();
    $question = Question::factory()->create();

    $this->actingAs($user)->post(route('answers.store', $question), ['body' => 'Eerste versie']);
    $this->actingAs($user)->post(route('answers.store', $question), ['body' => 'Tweede versie']);

    expect($user->answers()->count())->toBe(1)
        ->and($user->answers()->first()->body)->toBe('Tweede versie');
});

test('an answer is required', function () {
    $user = User::factory()->create();
    $question = Question::factory()->create();

    $this->actingAs($user)->post(route('answers.store', $question), ['body' => ''])
        ->assertSessionHasErrors('body');
});

test('a member can edit their own answer', function () {
    $user = User::factory()->create();
    $answer = Answer::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user)->get(route('answers.edit', $answer))->assertOk();
    $this->actingAs($user)->patch(route('answers.update', $answer), ['body' => 'Aangepast antwoord'])
        ->assertRedirect(route('answers.index'));

    expect($answer->refresh()->body)->toBe('Aangepast antwoord');
});

test('a member cannot edit someone elses answer', function () {
    $user = User::factory()->create();
    $answer = Answer::factory()->create();

    $this->actingAs($user)->get(route('answers.edit', $answer))->assertForbidden();
    $this->actingAs($user)->patch(route('answers.update', $answer), ['body' => 'Kaping'])->assertForbidden();
});

test('the profile links to your answers', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get(route('profile.show', $user))
        ->assertOk()
        ->assertSee(route('answers.index'));
});
