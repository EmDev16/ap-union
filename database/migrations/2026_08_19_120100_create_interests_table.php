<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * The interests members can pick from, grouped by category.
     *
     * @var array<string, array<int, string>>
     */
    private const INTERESTS = [
        'Outdoor & Fitness' => [
            'Bouldering', 'Trail running', 'Pickleball', 'Hot yoga', 'Backpacking', 'Calisthenics',
            'Cold plunging', 'Cycling', 'Paddleboarding', 'Snowboarding', 'Rock climbing',
            'Mountain biking', 'Pilates', 'Weightlifting', 'Trail hiking', 'Marathon training',
            'Parkour', 'Kayaking', 'Martial arts', 'Open-water swimming',
        ],
        'Food & Beverage' => [
            'Specialty coffee', 'Natural wine', 'Craft beer', 'Sourdough baking', 'Matcha brewing',
            'Mixology', 'Food pop-ups', 'Plant-based cooking', 'Meal prepping', 'Street food',
            'Espresso baristas', 'Fermentation', 'Fermented beverages', 'Charcuterie boards',
            'Specialty teas', 'Fine dining', 'Baking pastry', 'Wine tasting', 'Home brewing',
            'Olive oil',
        ],
        'Digital & Gaming' => [
            'PC gaming', 'Esports', 'Tabletop RPGs', 'Cozy gaming', 'Content creation',
            'Digital illustration', 'VR gaming', 'Podcasting', 'Internet culture', '3D modeling',
            'Retro gaming', 'Game development', 'Coding projects', 'Streaming', 'Tech repair',
            'Mobile gaming', 'Crypto tracking', 'Drone racing', 'Audio engineering', 'Vlogging',
        ],
        'Arts & Crafts' => [
            'Ceramics', 'Rug tufting', 'Film photography', 'Crochet', 'Garment upcycling',
            'Printmaking', 'Woodworking', 'Candle making', 'Scrapbooking', 'Pottery', 'Embroidery',
            'Calligraphy', 'Oil painting', 'Stained glass', 'Soap making', 'Origami',
            'Jewelry making', 'Leathercraft', 'Watercolor painting', 'Miniature painting',
        ],
        'Entertainment' => [
            'Anime', 'K-pop', 'Music festivals', 'Vinyl collecting', 'Reality TV', 'True crime',
            'Board games', 'Stand-up comedy', 'Indie cinema', 'Cosplay', 'Live theater',
            'Horror movies', 'Comic books', 'Sci-fi novels', 'Film criticism',
            'Symphony orchestra', 'Opera', 'Docuseries', 'Magic tricks', 'Open mics',
        ],
        'Lifestyle & Wellness' => [
            'Houseplants', 'Skincare', 'Tarot reading', 'Breathwork', 'Biohacking', 'Desk setups',
            'Van life', 'Zero-waste living', 'Interior design', 'Minimalist living',
            'Aromatherapy', 'Sound baths', 'Sauna culture', 'Cold therapy', 'Journaling',
            'Feng shui', 'Capsule wardrobes', 'Sustainable fashion', 'Fasting',
            'Sleep optimization',
        ],
        'Travel' => [
            'Solo travel', 'Digital nomadism', 'Road trips', 'Off-grid travel', 'Glamping',
            'Urban exploration', 'Scuba diving', 'Language learning', 'Culinary travel',
            'Eco-tourism', 'Camping', 'Hostel hopping', 'Train travel', 'Backpacking Asia',
            'Island hopping', 'Historical tours', 'Wildlife safaris', 'National parks',
            'Staycations', 'Mountain trekking',
        ],
        'Finance & Career' => [
            'Personal finance', 'Stock investing', 'Side hustles', 'Freelancing',
            'Career pivoting', 'House hacking', 'AI automation', 'FIRE movement',
            'Sneaker reselling', 'Networking', 'Real estate', 'Crypto trading', 'Micro-investing',
            'Budgeting apps', 'E-commerce', 'Stock trading', 'Affiliate marketing',
            'Resume polishing', 'Public speaking', 'Professional mentoring',
        ],
        'Social & Community' => [
            'Run clubs', 'Book clubs', 'Climate activism', 'Mutual aid', 'Pub trivia', 'Foraging',
            'Community gardening', 'Pet fostering', 'Social sports', 'Volunteer work',
            'Neighborhood watch', 'Animal shelter', 'Youth mentoring', 'Disaster relief',
            'Food banks', 'Beach cleanups', 'Parent groups', 'Toastmasters', 'Cultural societies',
            'Historical preservation',
        ],
        'Niche & Subcultures' => [
            'Watch collecting', 'Fragrance blending', 'Fine stationery', 'Audiophile gear',
            'Speedcubing', 'Birdwatching', 'Vintage fashion', 'FPV drones', 'LARPing',
            'Mechanical keyboards', 'Pen collecting', 'Pipe smoking', 'Mineral collecting',
            'Bonsai trees', 'Antiques hunting', 'Coin collecting', 'Stamp collecting',
            'Astrophotography', 'Knife making', 'Archery',
        ],
    ];

    public function up(): void
    {
        Schema::create('interests', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->string('category');
            $table->timestamps();
        });

        Schema::create('interest_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('interest_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unique(['interest_id', 'user_id']);
        });

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
        Schema::dropIfExists('interest_user');
        Schema::dropIfExists('interests');
    }
};
