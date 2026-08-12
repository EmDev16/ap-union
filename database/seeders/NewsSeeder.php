<?php

namespace Database\Seeders;

use App\Models\News;
use App\Models\User;
use Illuminate\Database\Seeder;

class NewsSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@ehb.be')->firstOrFail();
        News::firstOrCreate(['title' => 'Welkom bij AP Union'], ['user_id' => $admin->id, 'content' => 'Welkom op AP Union. Ontdek ideeën en gesprekken die ertoe doen.', 'published_at' => now()]);
    }
}
