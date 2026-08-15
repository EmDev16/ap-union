<?php

use App\Models\Faq;
use App\Models\FaqCategory;
use App\Models\User;

test('guests can view the public faq page', function () {
    $category = FaqCategory::factory()->create();
    Faq::factory()->create(['faq_category_id' => $category->id]);

    $this->get(route('faq.index'))->assertOk()->assertSee($category->name);
});

test('regular users cannot manage faqs', function () {
    $user = User::factory()->create(['is_admin' => false]);

    $this->actingAs($user)->get(route('admin.faqs.create'))->assertForbidden();
});

test('admins can create a faq', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $category = FaqCategory::factory()->create();

    $this->actingAs($admin)->post(route('admin.faqs.store'), [
        'faq_category_id' => $category->id,
        'question' => 'Nieuwe vraag?',
        'answer' => 'Dit is een antwoord.',
    ])->assertRedirect(route('admin.faqs.index'));

    $this->assertDatabaseHas('faqs', ['question' => 'Nieuwe vraag?']);
});
