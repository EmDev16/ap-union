<?php

use App\Models\Message;
use App\Models\Post;
use App\Models\PostAppeal;
use App\Models\User;

function reportedPost(User $admin, User $author): Post
{
    $post = Post::factory()->create(['user_id' => $author->id]);

    test()->actingAs($admin)->post(route('admin.posts.review', $post), ['reason' => 'Ongepast']);

    return $post->fresh();
}

test('the author contests a review and the admin has to explain the flag', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $author = User::factory()->create(['is_admin' => false]);
    $post = reportedPost($admin, $author);

    $this->actingAs($author)->post(route('posts.appeals.store', $post), [
        'reason' => 'Dit is gewoon mijn mening.',
    ])->assertRedirect();

    $appeal = PostAppeal::firstOrFail();
    expect($appeal->stage)->toBe(PostAppeal::CONTEST)
        ->and($appeal->admin_id)->toBe($admin->id)
        ->and($admin->notifications()->count())->toBe(1);

    $this->actingAs($admin)->get(route('admin.appeals.index'))
        ->assertOk()
        ->assertSee('Dit is gewoon mijn mening.');

    $this->actingAs($admin)->patch(route('admin.appeals.update', $appeal), [
        'response' => 'Je post viel onder haatspraak, daarom werd hij geflagd.',
    ])->assertRedirect();

    expect($appeal->fresh()->decision)->toBe(PostAppeal::EXPLAINED)
        ->and($appeal->fresh()->isOpen())->toBeFalse()
        ->and(Message::where('system_type', Message::REVIEW_EXPLAINED)->count())->toBe(1)
        ->and($post->fresh()->isUnderReview())->toBeTrue();
});

test('a second contest lets the admin remove the post', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $author = User::factory()->create(['is_admin' => false]);
    $post = reportedPost($admin, $author);

    $this->actingAs($author)->post(route('posts.appeals.store', $post));
    $this->actingAs($admin)->patch(route('admin.appeals.update', PostAppeal::firstOrFail()), [
        'response' => 'Uitleg van de admin.',
    ]);

    $this->actingAs($author)->post(route('posts.appeals.store', $post), ['reason' => 'Ik blijf erbij.'])
        ->assertRedirect();

    $second = PostAppeal::where('stage', PostAppeal::SECOND)->firstOrFail();

    $this->actingAs($admin)->patch(route('admin.appeals.update', $second), [
        'response' => 'Na een tweede lezing blijft dit tegen de regels.',
        'decision' => PostAppeal::REMOVED,
    ])->assertRedirect();

    expect($post->fresh()->isRemoved())->toBeTrue()
        ->and(Message::where('system_type', Message::REVIEW_REMOVED)->count())->toBe(1);

    $this->actingAs($author)->get(route('posts.show', $post))->assertForbidden();
});

test('a second contest can also convince the admin to publish the post again', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $author = User::factory()->create(['is_admin' => false]);
    $post = reportedPost($admin, $author);

    $this->actingAs($author)->post(route('posts.appeals.store', $post));
    $this->actingAs($admin)->patch(route('admin.appeals.update', PostAppeal::firstOrFail()), [
        'response' => 'Uitleg van de admin.',
    ]);
    $this->actingAs($author)->post(route('posts.appeals.store', $post), ['reason' => 'Ik blijf erbij.']);

    $this->actingAs($admin)->patch(route('admin.appeals.update', PostAppeal::where('stage', PostAppeal::SECOND)->firstOrFail()), [
        'response' => 'Je redenering overtuigt me, de post mag terug.',
        'decision' => PostAppeal::RESTORED,
    ]);

    expect($post->fresh()->isUnderReview())->toBeFalse()
        ->and($post->fresh()->isRemoved())->toBeFalse();
});

test('after a removal another random admin handles the last appeal', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $otherAdmin = User::factory()->create(['is_admin' => true]);
    $author = User::factory()->create(['is_admin' => false]);
    $post = reportedPost($admin, $author);

    $this->actingAs($author)->post(route('posts.appeals.store', $post));
    $this->actingAs($admin)->patch(route('admin.appeals.update', PostAppeal::firstOrFail()), [
        'response' => 'Uitleg van de admin.',
    ]);
    $this->actingAs($author)->post(route('posts.appeals.store', $post), ['reason' => 'Ik blijf erbij.']);
    $this->actingAs($admin)->patch(route('admin.appeals.update', PostAppeal::where('stage', PostAppeal::SECOND)->firstOrFail()), [
        'response' => 'Blijft tegen de regels.',
        'decision' => PostAppeal::REMOVED,
    ]);

    $this->actingAs($author)->post(route('posts.appeals.store', $post), [
        'reason' => 'Ik vraag een laatste blik van iemand anders.',
    ])->assertRedirect();

    $last = PostAppeal::where('stage', PostAppeal::AFTER_DELETE)->firstOrFail();
    expect($last->admin_id)->toBe($otherAdmin->id);

    $this->actingAs($admin)->patch(route('admin.appeals.update', $last), [
        'response' => 'Niet mijn dossier.',
        'decision' => PostAppeal::RESTORED,
    ])->assertForbidden();

    $this->actingAs($otherAdmin)->get(route('admin.appeals.index'))
        ->assertOk()
        ->assertSee('Ik vraag een laatste blik van iemand anders.')
        ->assertSee('Blijft tegen de regels.');

    $this->actingAs($otherAdmin)->patch(route('admin.appeals.update', $last), [
        'response' => 'Ik volg de redenering van de gebruiker, de post mag terug.',
        'decision' => PostAppeal::RESTORED,
    ])->assertRedirect();

    expect($post->fresh()->isRemoved())->toBeFalse()
        ->and($author->notifications()->count())->toBeGreaterThan(1);
});

test('the last admin can also keep the removal and then the review is closed', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $otherAdmin = User::factory()->create(['is_admin' => true]);
    $author = User::factory()->create(['is_admin' => false]);
    $post = reportedPost($admin, $author);

    $this->actingAs($author)->post(route('posts.appeals.store', $post));
    $this->actingAs($admin)->patch(route('admin.appeals.update', PostAppeal::firstOrFail()), ['response' => 'Uitleg.']);
    $this->actingAs($author)->post(route('posts.appeals.store', $post), ['reason' => 'Oneens.']);
    $this->actingAs($admin)->patch(route('admin.appeals.update', PostAppeal::where('stage', PostAppeal::SECOND)->firstOrFail()), [
        'response' => 'Blijft tegen de regels.',
        'decision' => PostAppeal::REMOVED,
    ]);
    $this->actingAs($author)->post(route('posts.appeals.store', $post), ['reason' => 'Laatste beroep.']);

    $last = PostAppeal::where('stage', PostAppeal::AFTER_DELETE)->firstOrFail();

    $this->actingAs($otherAdmin)->patch(route('admin.appeals.update', $last), [
        'response' => 'De verwijdering blijft.',
        'decision' => PostAppeal::UPHELD,
    ])->assertRedirect();

    expect($post->fresh()->isRemoved())->toBeTrue()
        ->and($post->fresh()->nextAppealStage())->toBeNull();

    $this->actingAs($author)->post(route('posts.appeals.store', $post), ['reason' => 'Nog eens.'])
        ->assertSessionHas('error');

    expect(PostAppeal::count())->toBe(3);
});

test('an admin must motivate every decision and only the author appeals', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $author = User::factory()->create(['is_admin' => false]);
    $stranger = User::factory()->create(['is_admin' => false]);
    $post = reportedPost($admin, $author);

    $this->actingAs($stranger)->post(route('posts.appeals.store', $post))->assertForbidden();

    $this->actingAs($author)->post(route('posts.appeals.store', $post));

    $this->actingAs($admin)->patch(route('admin.appeals.update', PostAppeal::firstOrFail()), ['response' => ''])
        ->assertSessionHasErrors('response');
});

test('a removed post stays hidden for everybody', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $author = User::factory()->create(['is_admin' => false]);
    $reader = User::factory()->create(['is_admin' => false]);
    $reader->following()->attach($author->id, ['accepted_at' => now()]);
    $post = Post::factory()->create(['user_id' => $author->id, 'content' => 'Verwijderde post']);
    $post->update(['removed_at' => now(), 'removed_by' => $admin->id]);

    $this->actingAs($reader)->get(route('explore'))->assertDontSee('Verwijderde post');
    $this->actingAs($reader)->get(route('home'))->assertDontSee('Verwijderde post');
    $this->actingAs($author)->get(route('profile.show', $author))->assertDontSee('Verwijderde post');
    $this->actingAs($admin)->get(route('admin.posts.index'))->assertSee('Verwijderde post');
});

test('an admin removes a reviewed post from the review list and can wipe it later', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $author = User::factory()->create(['is_admin' => false]);
    $post = reportedPost($admin, $author);

    $this->actingAs($admin)->get(route('admin.posts.index'))->assertOk()->assertSee('Post verwijderen');

    $this->actingAs($admin)->delete(route('admin.posts.remove', $post))->assertRedirect();

    expect($post->fresh()->isRemoved())->toBeTrue()
        ->and(Message::where('system_type', Message::REVIEW_REMOVED)->count())->toBe(1);

    $this->actingAs($author)->post(route('posts.appeals.store', $post), ['reason' => 'Laatste beroep.']);

    $this->actingAs($admin)->delete(route('admin.posts.purge', $post))->assertSessionHas('error');
    expect(Post::whereKey($post->id)->exists())->toBeTrue();

    $this->actingAs($admin)->patch(route('admin.appeals.update', PostAppeal::firstOrFail()), [
        'response' => 'De verwijdering blijft.',
        'decision' => PostAppeal::UPHELD,
    ]);

    $this->actingAs($admin)->delete(route('admin.posts.purge', $post))->assertRedirect();
    expect(Post::whereKey($post->id)->exists())->toBeFalse();
});

test('a member cannot remove or wipe posts', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $author = User::factory()->create(['is_admin' => false]);
    $post = reportedPost($admin, $author);

    $this->actingAs($author)->delete(route('admin.posts.remove', $post))->assertForbidden();
    $this->actingAs($author)->delete(route('admin.posts.purge', $post))->assertForbidden();
});
