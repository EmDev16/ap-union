<?php

namespace Database\Seeders;

use App\Models\Faq;
use App\Models\FaqCategory;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    public function run(): void
    {
        $category = FaqCategory::firstOrCreate(['name' => 'Algemeen']);

        Faq::firstOrCreate([
            'faq_category_id' => $category->id,
            'question' => 'Wat is AP Union?',
        ], [
            'answer' => 'AP Union is een sociaal platform gebaseerd op ideeën.',
        ]);
    }
}
