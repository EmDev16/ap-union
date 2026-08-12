<?php

namespace Database\Seeders;

class FaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

public function run(): void
{
    $category = \App\Models\FaqCategory::create(['name' => 'Algemeen']);
    \App\Models\Faq::create([
        'faq_category_id' => $category->id,
        'question' => 'Wat is AP Union?',
        'answer' => 'AP Union is een sociaal platform gebaseerd op ideeën.'
    ]);
}
}