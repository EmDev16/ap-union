<?php

use App\Models\Faq;
use App\Models\FaqCategory;
use App\Models\News;
use App\Models\User;

test('a contact message requires valid input', function () {
    $this->post(route('contact.store'), ['name' => '', 'email' => 'geen-email', 'subject' => '', 'message' => ''])
        ->assertSessionHasErrors(['name', 'email', 'subject', 'message']);

    $this->assertDatabaseCount('contacts', 0);
});

test('a post requires content or media', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->post(route('posts.store'), [])
        ->assertSessionHasErrors(['content', 'media']);

    $this->assertDatabaseCount('posts', 0);
});

test('creating a faq requires a question, an answer and an existing category', function () {
    $admin = User::factory()->create(['is_admin' => true]);

    $this->actingAs($admin)->post(route('admin.faqs.store'), ['faq_category_id' => 999, 'question' => '', 'answer' => ''])
        ->assertSessionHasErrors(['faq_category_id', 'question', 'answer']);

    $this->assertDatabaseCount('faqs', 0);
});

test('updating a faq requires valid input', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $faq = Faq::factory()->for(FaqCategory::factory(), 'category')->create();

    $this->actingAs($admin)->patch(route('admin.faqs.update', $faq), ['faq_category_id' => $faq->faq_category_id, 'question' => '', 'answer' => ''])
        ->assertSessionHasErrors(['question', 'answer']);
});

test('a faq category name must be unique', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    FaqCategory::factory()->create(['name' => 'Account']);

    $this->actingAs($admin)->post(route('admin.faq-categories.store'), ['name' => 'Account'])
        ->assertSessionHasErrors('name');
});

test('creating news requires a title, content and a publication date', function () {
    $admin = User::factory()->create(['is_admin' => true]);

    $this->actingAs($admin)->post(route('admin.news.store'), ['title' => '', 'content' => '', 'published_at' => 'geen-datum'])
        ->assertSessionHasErrors(['title', 'content', 'published_at']);

    $this->assertDatabaseCount('news', 0);
});

test('admins can update and delete news', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $news = News::factory()->create();

    $this->actingAs($admin)->patch(route('admin.news.update', $news), [
        'title' => 'Bijgewerkt bericht',
        'content' => 'Nieuwe inhoud.',
        'published_at' => now()->toDateTimeString(),
    ])->assertRedirect(route('admin.news.index'));

    $this->assertDatabaseHas('news', ['id' => $news->id, 'title' => 'Bijgewerkt bericht']);

    $this->actingAs($admin)->delete(route('admin.news.destroy', $news))->assertRedirect(route('admin.news.index'));
    $this->assertDatabaseMissing('news', ['id' => $news->id]);
});

test('an admin created user requires a unique email and a confirmed password', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $existing = User::factory()->create();

    $this->actingAs($admin)->post(route('admin.users.store'), [
        'name' => 'Nieuwe gebruiker',
        'username' => 'nieuwe gebruiker',
        'email' => $existing->email,
        'password' => 'wachtwoord',
        'password_confirmation' => 'ander-wachtwoord',
    ])->assertSessionHasErrors(['username', 'email', 'password']);
});

test('profile updates require a name and a valid email', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->patch(route('profile.update'), ['name' => '', 'email' => 'geen-email'])
        ->assertSessionHasErrors(['name', 'email']);
});
