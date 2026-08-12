<?php

namespace Database\Factories;

use App\Models\News;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class NewsFactory extends Factory
{
    protected $model = News::class;

    public function definition(): array
    {
        return ['user_id' => User::factory(), 'title' => fake()->sentence(), 'content' => fake()->paragraph(), 'published_at' => now(), 'image' => null];
    }
}
