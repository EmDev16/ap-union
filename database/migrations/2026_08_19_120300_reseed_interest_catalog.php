<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * The catalog members pick their interests from, grouped by category.
     *
     * @var array<string, array<int, string>>
     */
    private const INTERESTS = [
        'Outdoor & Fitness' => [
            'Bouldering', 'Trail running', 'Pickleball', 'Hot yoga', 'Backpacking', 'Calisthenics',
            'Cold plunging', 'Cycling', 'Snowboarding', 'Rock climbing', 'Mountain biking',
            'Pilates', 'Weightlifting', 'Marathon training', 'Kayaking', 'Martial arts',
            'Open-water swimming',
        ],
        'Digital & Gaming' => [
            'PC gaming', 'Esports', 'Tabletop RPGs', 'Cozy gaming', 'Content creation',
            'Digital illustration', 'VR gaming', 'Podcasting', 'Internet culture', '3D modeling',
            'Retro gaming', 'Game development', 'Coding projects', 'Streaming', 'Mobile gaming',
            'Audio engineering', 'Vlogging',
        ],
        'Arts & Crafts' => [
            'Ceramics', 'Film photography', 'Crochet', 'Garment upcycling', 'Printmaking',
            'Woodworking', 'Candle making', 'Pottery', 'Embroidery', 'Calligraphy', 'Oil painting',
            'Soap making', 'Jewelry making', 'Leathercraft', 'Watercolor painting',
            'Miniature painting',
        ],
        'Entertainment' => [
            'Anime', 'K-pop', 'Music festivals', 'Vinyl collecting', 'Reality TV', 'True crime',
            'Board games', 'Stand-up comedy', 'Indie cinema', 'Cosplay', 'Live theater',
            'Horror movies', 'Comic books', 'Sci-fi novels', 'Film criticism', 'Open mics', 'Magic tricks',
        ],
        'Lifestyle & Wellness' => [
            'Houseplants', 'Skincare', 'Tarot reading', 'Breathwork', 'Biohacking', 'Desk setups',
            'Van life', 'Zero-waste living', 'Interior design', 'Minimalist living',
            'Sauna culture', 'Journaling', 'Capsule wardrobes', 'Sustainable fashion',
            'Sleep optimization', 'Sound baths', 'Aromatherapy',
        ],
        'Travel' => [
            'Solo travel', 'Digital nomadism', 'Road trips', 'Off-grid travel', 'Glamping',
            'Urban exploration', 'Scuba diving', 'Language learning', 'Culinary travel', 'Camping',
            'Hostel hopping', 'Train travel', 'Island hopping', 'National parks', 'Staycations',
            'Mountain trekking',
        ],
        'Finance & Career' => [
            'Personal finance', 'Stock investing', 'Side hustles', 'Freelancing',
            'Career pivoting', 'AI automation', 'FIRE movement', 'Sneaker reselling', 'Networking',
            'Real estate', 'Crypto trading', 'Budgeting apps', 'E-commerce', 'Affiliate marketing',
            'Public speaking', 'Professional mentoring', 'Micro-investing',
        ],
        'Social & Community' => [
            'Run clubs', 'Book clubs', 'Climate activism', 'Mutual aid', 'Pub trivia', 'Foraging',
            'Community gardening', 'Pet fostering', 'Social sports', 'Volunteer work',
            'Animal shelter', 'Youth mentoring', 'Food banks', 'Beach cleanups',
            'Cultural societies', 'Historical preservation',
        ],
        'Niche & Subcultures' => [
            'Watch collecting', 'Fragrance blending', 'Fine stationery', 'Audiophile gear',
            'Speedcubing', 'Birdwatching', 'Vintage fashion', 'FPV drones', 'LARPing',
            'Mechanical keyboards', 'Bonsai trees', 'Antiques hunting', 'Astrophotography',
            'Knife making', 'Archery', 'Coin collecting', 'Pen collecting',
        ],
    ];

    public function up(): void
    {
        if (! Schema::hasColumn('interests', 'category')) {
            Schema::table('interests', function (Blueprint $table) {
                $table->string('category')->default('')->after('slug');
            });
        }

        DB::table('interest_user')->delete();
        DB::table('interests')->delete();

        $rows = [];

        foreach (self::INTERESTS as $category => $names) {
            foreach ($names as $name) {
                $rows[] = [
                    'name' => $name,
                    'slug' => Str::slug($name),
                    'category' => $category,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        DB::table('interests')->insert($rows);
    }

    public function down(): void
    {
        DB::table('interest_user')->delete();
        DB::table('interests')->delete();

        Schema::table('interests', function (Blueprint $table) {
            $table->dropColumn('category');
        });
    }
};
