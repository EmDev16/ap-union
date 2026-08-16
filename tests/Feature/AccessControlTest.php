<?php

use App\Models\Faq;
use App\Models\FaqCategory;
use App\Models\News;
use App\Models\User;

test('guests can view the public pages', function (string $uri) {
    $this->get($uri)->assertOk();
})->with(['/', '/faq', '/news', '/contact', '/explore', '/messages', '/search']);

test('guests can view a public profile', function () {
    $user = User::factory()->create();

    $this->get(route('profile.show', $user))->assertOk();
});

test('guests are redirected to login on member pages', function (string $uri) {
    $this->get($uri)->assertRedirect(route('login'));
})->with(['/dashboard', '/feed', '/profile', '/posts/create']);

test('guests are redirected to login on admin pages', function (string $routeName) {
    $this->get(route($routeName))->assertRedirect(route('login'));
})->with(['admin.faqs.index', 'admin.faq-categories.index', 'admin.news.index', 'admin.contacts.index', 'admin.users.index']);

test('regular users cannot open admin pages', function (string $routeName) {
    $user = User::factory()->create();

    $this->actingAs($user)->get(route($routeName))->assertForbidden();
})->with(['admin.faqs.index', 'admin.faq-categories.index', 'admin.news.index', 'admin.contacts.index', 'admin.users.index']);

test('admins can open admin pages', function (string $routeName) {
    $admin = User::factory()->create(['is_admin' => true]);

    $this->actingAs($admin)->get(route($routeName))->assertOk();
})->with(['admin.faqs.index', 'admin.faq-categories.index', 'admin.news.index', 'admin.contacts.index', 'admin.users.index']);

test('regular users cannot update or delete faqs and news', function () {
    $user = User::factory()->create();
    $faq = Faq::factory()->for(FaqCategory::factory(), 'category')->create();
    $news = News::factory()->create();

    $this->actingAs($user)->patch(route('admin.faqs.update', $faq), [])->assertForbidden();
    $this->actingAs($user)->delete(route('admin.faqs.destroy', $faq))->assertForbidden();
    $this->actingAs($user)->patch(route('admin.news.update', $news), [])->assertForbidden();
    $this->actingAs($user)->delete(route('admin.news.destroy', $news))->assertForbidden();
});
