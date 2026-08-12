<?php

use App\Models\News;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('guests can view published news', function () {
    $news = News::factory()->create(['published_at' => now()]);

    $this->get(route('news.index'))->assertOk()->assertSee($news->title);
    $this->get(route('news.show', $news))->assertOk()->assertSee($news->content);
});

test('guests cannot create news', function () {
    $this->post(route('admin.news.store'), [])->assertRedirect(route('login'));
});

test('admins can create news with an image', function () {
    Storage::fake('public');
    $admin = User::factory()->create(['is_admin' => true]);

    $this->actingAs($admin)->post(route('admin.news.store'), [
        'title' => 'Nieuw bericht',
        'content' => 'Dit is een nieuwsbericht.',
        'published_at' => now()->format('Y-m-d H:i:s'),
        'image' => UploadedFile::fake()->image('news.jpg'),
    ])->assertRedirect(route('admin.news.index'));

    $news = News::where('title', 'Nieuw bericht')->firstOrFail();
    Storage::disk('public')->assertExists($news->image);
});
