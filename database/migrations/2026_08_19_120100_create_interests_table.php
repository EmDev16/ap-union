<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * The interests members can pick from.
     *
     * @var array<int, string>
     */
    private const INTERESTS = [
        'Architecture', 'Art', 'Artificial intelligence', 'Astronomy', 'Baking', 'Basketball',
        'Biology', 'Board games', 'Books', 'Business', 'Chemistry', 'Cinema', 'Climate', 'Coding',
        'Cooking', 'Cybersecurity', 'Dance', 'Design', 'Economics', 'Education', 'Engineering',
        'Entrepreneurship', 'Fashion', 'Festivals', 'Finance', 'Fitness', 'Football', 'Gaming',
        'Gardening', 'Geography', 'Health', 'History', 'Hiking', 'Human rights', 'Journalism',
        'Languages', 'Law', 'Literature', 'Marketing', 'Mathematics', 'Medicine', 'Music',
        'Nature', 'Nutrition', 'Philosophy', 'Photography', 'Physics', 'Podcasts', 'Politics',
        'Psychology', 'Robotics', 'Running', 'Science', 'Sociology', 'Space', 'Sports',
        'Startups', 'Statistics', 'Sustainability', 'Swimming', 'Teaching', 'Technology',
        'Theatre', 'Travel', 'Volunteering', 'Writing', 'Yoga',
    ];

    public function up(): void
    {
        Schema::create('interests', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::create('interest_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('interest_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unique(['interest_id', 'user_id']);
        });

        DB::table('interests')->insert(array_map(fn (string $name) => [
            'name' => $name,
            'slug' => Str::slug($name),
            'created_at' => now(),
            'updated_at' => now(),
        ], self::INTERESTS));
    }

    public function down(): void
    {
        Schema::dropIfExists('interest_user');
        Schema::dropIfExists('interests');
    }
};
