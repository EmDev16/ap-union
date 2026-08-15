<?php

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('users can create a post with text and media', function () {
    Storage::fake('public');
    $user = User::factory()->create();

    $this->actingAs($user)->post(route('posts.store'), [
        'content' => 'Mijn idee met een foto.',
        'media' => [UploadedFile::fake()->image('idea.jpg')],
    ])->assertRedirect(route('profile.show', $user));

    $post = Post::where('user_id', $user->id)->firstOrFail();
    expect($post->media)->toHaveCount(1);
    Storage::disk('public')->assertExists($post->media->first()->path);
});

test('users can create more than three posts', function () {
    $user = User::factory()->create();
    Post::factory()->count(3)->create(['user_id' => $user->id]);

    $this->actingAs($user)->post(route('posts.store'), ['content' => 'Een vierde post'])
        ->assertRedirect(route('profile.show', $user));

    $this->assertDatabaseHas('posts', ['user_id' => $user->id, 'content' => 'Een vierde post']);
});

test('users cannot delete posts from another user', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create();

    $this->actingAs($user)->delete(route('posts.destroy', $post))->assertForbidden();
});
