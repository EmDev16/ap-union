<?php

namespace Database\Seeders;

use App\Models\Answer;
use App\Models\Comment;
use App\Models\Contact;
use App\Models\Conversation;
use App\Models\Interest;
use App\Models\Like;
use App\Models\Message;
use App\Models\Post;
use App\Models\Question;
use App\Models\User;
use App\Notifications\FollowRequested;
use App\Notifications\PostCommented;
use App\Notifications\PostLiked;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    use WithoutModelEvents;

    private const ADMINS = 10;

    private const MEMBERS = 50;

    /**
     * @var array<int, string>
     */
    private const ABOUT = [
        'Student urban planning, always up for a walk through a neighbourhood I do not know yet.',
        'I read two books a month and I would rather discuss them than rate them.',
        'Trail runner and slow cook. Ask me about routes around the city.',
        'Working on my first side project, learning in public.',
        'Film school dropout, still watching everything with subtitles.',
        'I like long conversations about small things.',
        'Climbing gym regular, terrible at resting days.',
        'Collecting records and opinions about them.',
        'Teaching myself Portuguese, one podcast at a time.',
        'Here for good questions, not for hot takes.',
        'Weekend potter, weekday spreadsheet person.',
        'Trying to keep my plants and my routines alive.',
        'Photography with film because waiting is part of it.',
        'I moved cities twice this year and I am still unpacking.',
        'Volunteering at the animal shelter on Saturdays.',
    ];

    /**
     * @var array<int, string>
     */
    private const POSTS = [
        'Started a small run club in my neighbourhood. Three people showed up the first week, eleven this week. Turns out most of us just needed a reason to leave the house.',
        'Question for the readers here: do you finish books you do not enjoy? I used to force myself through, now I stop at page fifty and I read twice as much.',
        'Spent the weekend rebuilding my desk setup. Fewer cables, one lamp, no second screen. My focus is noticeably better, which I did not expect.',
        'Went bouldering for the first time in a year. My forearms are gone but my head is clearer than it has been in weeks.',
        'I am learning that networking is mostly just staying in touch with people you already liked. Sent five messages this week, got four replies and one coffee.',
        'Anyone else find that journaling works better in the morning? Evening entries turn into complaining, morning entries turn into plans.',
        'My first ceramics piece came out of the kiln crooked and I love it more than anything I have bought this year.',
        'Three months of tracking every euro. The surprise was not the coffee, it was the subscriptions I forgot existed.',
        'Watched an indie film with no plot and thought about it for two days. Recommendations welcome, the stranger the better.',
        'Started a book club with four colleagues. Rule one: nobody has to finish the book. Attendance went up immediately.',
        'Trying cold showers for thirty days. Day nine and I still hate the first ten seconds, but I stopped snoozing my alarm.',
        'Moved my first side project from a notebook to actual code this weekend. It is ugly and it works.',
        'Went to a vinyl fair and left with two records and a long conversation about seventies jazz with a stranger.',
        'Does anyone here actually enjoy public speaking, or do we all just get slightly less scared over time?',
        'Camped without signal for two nights. I noticed how often I reached for a phone that was not in my pocket.',
        'Learning a language as an adult is humbling. I sound like a polite robot but the bakery understands me now.',
        'Repaired my own laptop fan instead of replacing the machine. Twelve euros and one hour, still working.',
        'The best conversation I had this month was in a queue. We talked about why nobody talks in queues.',
        'Started sketching on the train instead of scrolling. My drawings are bad and my commute feels shorter.',
        'Hosted a dinner where everyone had to bring a question instead of a bottle. Best evening in months.',
        'I keep a list of things I want to learn. This year: swimming properly, basic woodworking and how to say no.',
        'Volunteered at a beach cleanup. Two hours, forty people, an unreasonable amount of bottle caps.',
        'Reading about urban planning changed how I walk through my own city. Benches are political.',
        'Got my first plant to flower. Two years of guessing and apparently it just wanted less attention.',
        'Anybody here into speedcubing? I am stuck at forty seconds and my patience is stuck with me.',
        'Switched from listening to podcasts at double speed back to normal. I remember much more now.',
        'My weekly rule: one evening with no screens. It is the only appointment I never cancel.',
        'Started writing letters to two friends who moved abroad. Slower than messaging, somehow closer.',
        'Tried an open mic for the first time. Four minutes felt like forty, would do it again next month.',
        'Learning to weld at a community workshop. The instructor is seventy and funnier than anyone I follow online.',
        'The gym at six in the morning is a completely different place. Nobody talks and everybody nods.',
        'Sold my old camera to someone who is just starting out. Gave the lens away with it, felt right.',
        'What are people reading about climate that is not doom? I want something practical for once.',
        'Discovered that my city has a free repair cafe every month. Fixed a toaster and made two friends.',
        'Six months of consistent sleep and my whole personality apparently improved. Who knew.',
        'I finally understand index funds and I am slightly annoyed nobody explained it in school.',
        'Made a playlist for walking home at night. Twenty-two songs, all slow, all reliable.',
        'Started saying yes to invitations I would normally decline. Two were awkward, four were great.',
        'Anyone doing tabletop RPGs here? Our group needs one more player and infinite snacks.',
        'Wrote down every idea I had for a week. Ninety percent nonsense, but two are worth building.',
        'Rebuilt my bike after watching a repair video ten times. It brakes now, which is the important part.',
        'Talked to my neighbour for the first time in three years. He restores clocks. I had no idea.',
        'Museums on weekday mornings are empty and it changes the entire experience.',
        'Trying to read one long article properly instead of ten headlines badly.',
        'Signed up for a first aid course. Should probably have done that a decade ago.',
        'A friend asked me what I do for fun and I could not answer. Working on that list now.',
        'Cooking the same three dishes well beats cooking twenty badly. My kitchen is calmer.',
        'Went to a lecture about astrophotography and now I am pricing tripods I cannot justify.',
        'Learning that most productivity advice is just people describing their own personality.',
        'Started mentoring a student at my old school. I get more out of the hour than they do.',
    ];

    /**
     * @var array<int, string>
     */
    private const COMMENTS = [
        'This is exactly what I needed to read today.',
        'How did you get started? I keep postponing it.',
        'Same experience here, although it took me much longer.',
        'Curious what changed the most for you after a month.',
        'Saving this, I want to try the same thing in my city.',
        'Completely agree with the last part.',
        'Do you have a recommendation for a beginner?',
        'I tried this and failed twice, third time worked.',
        'Great point, I never looked at it that way.',
        'Any tips for doing this without spending much?',
        'This convinced me to finally book it.',
        'What would you do differently if you started again?',
        'Been thinking about this all week, thanks for writing it down.',
        'My experience was the opposite, happy to explain why.',
        'Adding this to my list for next month.',
    ];

    /**
     * @var array<int, string>
     */
    private const REPLIES = [
        'Start small, that was the only thing that worked for me.',
        'Thanks, that is a fair question. I will write a longer post about it.',
        'Please do, and let me know how it goes.',
        'Happy to share what I used, send me a message.',
        'Good luck, the first week is the hardest part.',
    ];

    /**
     * @var array<int, array{0: string, 1: string}>
     */
    private const QUESTIONS = [
        ['Which conversation changed your mind this year?', 'Tell us about a discussion that made you rethink something you were sure about.'],
        ['What is a skill you picked up outside of school or work?', 'Share how you learned it and what surprised you.'],
        ['How do you meet new people in a new city?', 'Concrete tips only, we can all use them.'],
        ['Which book would you give to a stranger?', 'One title and one sentence about why.'],
        ['What does a good weekend look like for you?', 'No wrong answers, we are curious about the details.'],
        ['Which habit actually stuck?', 'And which one did you give up on without regret?'],
        ['What is worth spending money on?', 'And what turned out to be a waste in your experience.'],
        ['How do you disconnect?', 'Describe what you do when you need distance from screens.'],
        ['What are you building right now?', 'Side projects, studies, renovations, anything counts.'],
        ['Who taught you something you still use?', 'A teacher, a colleague, a neighbour, a family member.'],
    ];

    /**
     * @var array<int, string>
     */
    private const ANSWERS = [
        'A colleague asked me why I always do things in the same order. I had no answer, so I changed the order.',
        'My grandmother taught me to repair clothes. I use it more than anything I learned in a classroom.',
        'Joining a sports club was the only thing that worked. Same people, same hour, every week.',
        'I would give away the book that made me start reading again after years of not finishing anything.',
        'A slow morning, a long walk and one plan for the evening. Anything more and it stops being a weekend.',
        'Walking after dinner stuck. Meditation did not, and I stopped pretending otherwise.',
        'Good shoes and good tools. Everything else can be second hand.',
        'I leave my phone in another room and cook something that needs attention.',
        'A small website where I keep track of the places I want to visit in my own city.',
        'A teacher who told me to write like I talk. It took ten years before I understood what he meant.',
    ];

    /**
     * @var array<int, array{0: string, 1: string, 2: string}>
     */
    private const CONTACTS = [
        ['question', 'How do I change my username?', 'I picked a username when I registered and I would like to change it, is that possible?'],
        ['feedback', 'The feed is refreshingly quiet', 'Just wanted to say that the chronological feed is the reason I keep coming back.'],
        ['question', 'Can I see who visited my profile?', 'I was wondering if there is a way to see which members looked at my profile.'],
        ['feedback', 'More interests please', 'The interest list is good but I would love to see a few more niche options.'],
        ['question', 'Why is my post under review?', 'I posted about a local event and it disappeared, could you tell me what happened?'],
        ['feedback', 'Messages work well', 'The conversation view is clear and I like that I can remove a conversation only for myself.'],
    ];

    /**
     * @var array<int, string>
     */
    private const CHAT = [
        'Hey, saw your post about the run club. Is it open for new people?',
        'It is, we meet on Wednesday at seven near the park entrance.',
        'Perfect, I will be there. Do people run in groups by pace?',
        'Two groups, one slow one slightly less slow. Nobody gets left behind.',
        'Sounds good, see you Wednesday.',
    ];

    public function run(): void
    {
        $admins = $this->createUsers(self::ADMINS, true);
        $members = $this->createUsers(self::MEMBERS, false);

        $this->attachInterests($members);
        $this->connect($admins, $members);

        $posts = $this->createPosts($members);
        $this->engage($posts, $members);
        $this->createQuestions($admins, $members);
        $this->createConversations($members);
        $this->createContacts($members);
    }

    /**
     * @return Collection<int, User>
     */
    private function createUsers(int $amount, bool $isAdmin): Collection
    {
        return User::factory($amount)
            ->sequence(fn ($sequence) => [
                'username' => ($isAdmin ? 'admin' : 'member').($sequence->index + 1),
                'email' => ($isAdmin ? 'admin' : 'member').($sequence->index + 1).'@ap-union.test',
                'password' => Hash::make('Password!321'),
                'is_admin' => $isAdmin,
                'about_me' => $isAdmin ? 'Part of the AP Union team.' : self::ABOUT[$sequence->index % count(self::ABOUT)],
                'created_at' => now()->subDays(200 - $sequence->index),
            ])
            ->create();
    }

    /**
     * @param  Collection<int, User>  $members
     */
    private function attachInterests(Collection $members): void
    {
        $interests = Interest::pluck('id');

        if ($interests->isEmpty()) {
            return;
        }

        foreach ($members as $member) {
            $member->interests()->sync($interests->random(min(User::MAX_INTERESTS, $interests->count())));
        }
    }

    /**
     * Admins follow admins, members follow members, with a few pending requests.
     *
     * @param  Collection<int, User>  $admins
     * @param  Collection<int, User>  $members
     */
    private function connect(Collection $admins, Collection $members): void
    {
        foreach ([$admins->keyBy('id'), $members->keyBy('id')] as $group) {
            foreach ($group as $user) {
                $others = $group->except($user->id)->random(min(8, $group->count() - 1))->values();

                foreach ($others as $index => $other) {
                    $user->allFollowing()->syncWithoutDetaching([
                        $other->id => ['accepted_at' => $index === 0 ? null : now()->subDays(random_int(1, 90))],
                    ]);

                    if ($index === 0) {
                        $other->notify(new FollowRequested($user));
                    }
                }
            }
        }
    }

    /**
     * @param  Collection<int, User>  $members
     * @return Collection<int, Post>
     */
    private function createPosts(Collection $members): Collection
    {
        $posts = collect();

        foreach (self::POSTS as $index => $content) {
            $author = $members[$index % $members->count()];

            $posts->push(Post::create([
                'user_id' => $author->id,
                'content' => $content,
                'is_showcased' => $index % 5 === 0,
                'created_at' => now()->subDays(60 - ($index % 60))->subHours($index % 24),
                'updated_at' => now()->subDays(60 - ($index % 60)),
            ]));
        }

        return $posts;
    }

    /**
     * @param  Collection<int, Post>  $posts
     * @param  Collection<int, User>  $members
     */
    private function engage(Collection $posts, Collection $members): void
    {
        $recent = $posts->sortByDesc('created_at')->take(15)->pluck('id');
        $pool = $members->keyBy('id');

        foreach ($posts as $index => $post) {
            foreach ($pool->except($post->user_id)->random(random_int(2, 12))->values() as $position => $liker) {
                Like::create(['post_id' => $post->id, 'user_id' => $liker->id]);

                if ($position === 0 && $recent->contains($post->id)) {
                    $post->user->notify(new PostLiked($liker, $post));
                }
            }

            $commenters = $pool->except($post->user_id)->random(random_int(0, 3))->values();

            foreach ($commenters as $position => $commenter) {
                $comment = Comment::create([
                    'post_id' => $post->id,
                    'user_id' => $commenter->id,
                    'content' => self::COMMENTS[($index + $position) % count(self::COMMENTS)],
                    'created_at' => $post->created_at->addHours($position + 1),
                ]);

                if ($recent->contains($post->id)) {
                    $post->user->notify(new PostCommented($commenter, $comment));
                }

                if ($position === 0) {
                    Comment::create([
                        'post_id' => $post->id,
                        'user_id' => $post->user_id,
                        'parent_id' => $comment->id,
                        'content' => self::REPLIES[$index % count(self::REPLIES)],
                        'created_at' => $comment->created_at->addHour(),
                    ]);
                }
            }
        }
    }

    /**
     * @param  Collection<int, User>  $admins
     * @param  Collection<int, User>  $members
     */
    private function createQuestions(Collection $admins, Collection $members): void
    {
        foreach (self::QUESTIONS as $index => [$title, $description]) {
            $question = Question::create([
                'user_id' => $admins[$index % $admins->count()]->id,
                'title' => $title,
                'description' => $description,
                'answers_publish_on' => $index < 5 ? now()->subDays(10 - $index)->toDateString() : now()->addDays($index)->toDateString(),
                'created_at' => now()->subDays(40 - $index * 2),
            ]);

            foreach ($members->random(random_int(4, 10))->values() as $member) {
                Answer::create([
                    'question_id' => $question->id,
                    'user_id' => $member->id,
                    'body' => self::ANSWERS[$index % count(self::ANSWERS)],
                    'created_at' => $question->created_at->addDay(),
                    'updated_at' => $question->created_at->addDay(),
                ]);
            }
        }
    }

    /**
     * @param  Collection<int, User>  $members
     */
    private function createConversations(Collection $members): void
    {
        for ($pair = 0; $pair < 10; $pair++) {
            $participants = $members->random(2)->values();
            $conversation = Conversation::create(['last_message_at' => now()->subDays($pair)]);
            $conversation->participants()->attach($participants->pluck('id'));

            foreach (self::CHAT as $index => $body) {
                Message::create([
                    'conversation_id' => $conversation->id,
                    'user_id' => $participants[$index % 2]->id,
                    'body' => $body,
                    'created_at' => now()->subDays($pair)->addMinutes($index * 3),
                    'updated_at' => now()->subDays($pair)->addMinutes($index * 3),
                ]);
            }
        }
    }

    /**
     * @param  Collection<int, User>  $members
     */
    private function createContacts(Collection $members): void
    {
        foreach (self::CONTACTS as $index => [$type, $subject, $message]) {
            $sender = $members[$index % $members->count()];

            Contact::create([
                'name' => $sender->name,
                'email' => $sender->email,
                'type' => $type,
                'subject' => $subject,
                'message' => $message,
                'is_answered' => $index < 2,
                'reply' => $index < 2 ? 'Thanks for reaching out, we answered your question by mail as well.' : null,
                'replied_at' => $index < 2 ? now()->subDays($index + 1) : null,
                'created_at' => now()->subDays(20 - $index),
            ]);
        }
    }
}
