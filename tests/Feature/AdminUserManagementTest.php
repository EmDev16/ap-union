<?php

use App\Models\User;

test('regular users cannot manage users', function () {
    $user = User::factory()->create(['is_admin' => false]);

    $this->actingAs($user)->get(route('admin.users.index'))->assertForbidden();
});

test('admins can manually create a user', function () {
    $admin = User::factory()->create(['is_admin' => true]);

    $this->actingAs($admin)->post(route('admin.users.store'), [
        'name' => 'New Member',
        'username' => 'new-member',
        'email' => 'member@example.com',
        'password' => 'StrongPassword!123',
        'password_confirmation' => 'StrongPassword!123',
        'is_admin' => '1',
    ])->assertRedirect(route('admin.users.index'));

    $this->assertDatabaseHas('users', ['email' => 'member@example.com', 'is_admin' => true]);
});

test('admins cannot change their own role or remove the last admin', function () {
    $admin = User::factory()->create(['is_admin' => true]);

    $this->actingAs($admin)->patch(route('admin.users.toggle-admin', $admin))
        ->assertSessionHas('error');
});
