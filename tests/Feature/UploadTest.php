<?php

use App\Models\News;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('users can upload a profile photo', function () {
    Storage::fake('public');
    $user = User::factory()->create();

    $this->actingAs($user)->patch(route('profile.update'), [
        'name' => $user->name,
        'email' => $user->email,
        'profile_photo' => UploadedFile::fake()->image('avatar.jpg'),
    ])->assertRedirect(route('profile.edit'));

    $user->refresh();
    expect($user->profile_photo)->not->toBeNull();
    Storage::disk('public')->assertExists($user->profile_photo);
});

test('a profile photo must be an image', function () {
    Storage::fake('public');
    $user = User::factory()->create();

    $this->actingAs($user)->patch(route('profile.update'), [
        'name' => $user->name,
        'email' => $user->email,
        'profile_photo' => UploadedFile::fake()->create('document.pdf', 100, 'application/pdf'),
    ])->assertSessionHasErrors('profile_photo');

    expect($user->refresh()->profile_photo)->toBeNull();
});

test('post media must be an image or a video', function () {
    Storage::fake('public');
    $user = User::factory()->create();

    $this->actingAs($user)->post(route('posts.store'), [
        'content' => 'Een post met een fout bestand.',
        'media' => [UploadedFile::fake()->create('document.pdf', 100, 'application/pdf')],
    ])->assertSessionHasErrors('media.0');

    $this->assertDatabaseCount('posts', 0);
});

test('a news image must be an image', function () {
    Storage::fake('public');
    $admin = User::factory()->create(['is_admin' => true]);

    $this->actingAs($admin)->post(route('admin.news.store'), [
        'title' => 'Nieuws met fout bestand',
        'content' => 'Inhoud.',
        'published_at' => now()->toDateTimeString(),
        'image' => UploadedFile::fake()->create('document.pdf', 100, 'application/pdf'),
    ])->assertSessionHasErrors('image');

    $this->assertDatabaseCount('news', 0);
});

test('updating news replaces the old image', function () {
    Storage::fake('public');
    $admin = User::factory()->create(['is_admin' => true]);
    $news = News::factory()->create(['image' => UploadedFile::fake()->image('old.jpg')->store('news', 'public')]);
    $oldImage = $news->image;

    $this->actingAs($admin)->patch(route('admin.news.update', $news), [
        'title' => $news->title,
        'content' => $news->content,
        'published_at' => now()->toDateTimeString(),
        'image' => UploadedFile::fake()->image('new.jpg'),
    ])->assertRedirect(route('admin.news.index'));

    $news->refresh();
    expect($news->image)->not->toBe($oldImage);
    Storage::disk('public')->assertExists($news->image);
    Storage::disk('public')->assertMissing($oldImage);
});
