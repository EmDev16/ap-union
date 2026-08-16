<?php

use App\Mail\ContactAnswered;
use App\Models\Contact;
use App\Models\Message;
use App\Models\Post;
use App\Models\Question;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

test('an admin lands on the admin home with contacts, numbers and notifications', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    Contact::create([
        'name' => 'Lid',
        'email' => 'lid@example.com',
        'type' => 'question',
        'subject' => 'Vraag over AP Union',
        'message' => 'Hoe werkt dit?',
    ]);

    $this->actingAs($admin)->get(route('home'))->assertRedirect(route('admin.home'));

    $this->actingAs($admin)->get(route('admin.home'))
        ->assertOk()
        ->assertSee('Vraag over AP Union')
        ->assertSee('Cijfers')
        ->assertSee('Notifications');
});

test('members cannot open the admin home', function () {
    $user = User::factory()->create(['is_admin' => false]);

    $this->actingAs($user)->get(route('admin.home'))->assertForbidden();
});

test('an admin answers a contact form from the panel', function () {
    Mail::fake();
    $admin = User::factory()->create(['is_admin' => true]);
    $contact = Contact::create([
        'name' => 'Lid',
        'email' => 'lid@example.com',
        'type' => 'feedback',
        'subject' => 'Feedback',
        'message' => 'Leuke site.',
    ]);

    $this->actingAs($admin)
        ->post(route('admin.contacts.reply', $contact), ['reply' => 'Bedankt voor je bericht.'])
        ->assertRedirect();

    expect($contact->fresh()->reply)->toBe('Bedankt voor je bericht.')
        ->and($contact->fresh()->is_answered)->toBeTrue();

    Mail::assertSent(ContactAnswered::class);
});

test('an admin creates questions instead of posts', function () {
    $admin = User::factory()->create(['is_admin' => true]);

    $this->actingAs($admin)->get(route('posts.create'))->assertForbidden();

    $this->actingAs($admin)->post(route('admin.questions.store'), [
        'title' => 'Wat vind je van AP Union?',
        'description' => 'Laat het ons weten.',
    ])->assertRedirect(route('admin.questions.index'));

    $this->assertDatabaseHas('questions', [
        'user_id' => $admin->id,
        'title' => 'Wat vind je van AP Union?',
    ]);
});

test('a member cannot create or edit questions', function () {
    $user = User::factory()->create(['is_admin' => false]);
    $question = Question::factory()->create();

    $this->actingAs($user)->get(route('admin.questions.create'))->assertForbidden();
    $this->actingAs($user)->post(route('admin.questions.store'), ['title' => 'Mag niet'])->assertForbidden();
    $this->actingAs($user)->patch(route('admin.questions.update', $question), ['title' => 'Mag niet'])->assertForbidden();
});

test('an admin question shows up on the home page of a member', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $member = User::factory()->create(['is_admin' => false]);
    Question::factory()->create(['user_id' => $admin->id, 'title' => 'Vraag van de admin']);

    $this->actingAs($member)->get(route('home'))->assertOk()->assertSee('Vraag van de admin');
});

test('an admin only follows other admins', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $other = User::factory()->create(['is_admin' => true]);
    $member = User::factory()->create(['is_admin' => false]);

    $this->actingAs($admin)->post(route('users.follow', $member));
    expect($admin->following()->count())->toBe(0);

    $this->actingAs($admin)->post(route('users.follow', $other));
    expect($admin->following()->pluck('users.id')->all())->toBe([$other->id]);
});

test('a member cannot follow an admin', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $member = User::factory()->create(['is_admin' => false]);

    $this->actingAs($member)->post(route('users.follow', $admin));

    expect($member->following()->count())->toBe(0);
});

test('an admin messages any user but does not comment or like', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $member = User::factory()->create(['is_admin' => false]);
    $post = Post::factory()->create(['user_id' => $member->id]);

    $this->actingAs($admin)->post(route('messages.store'), ['user_id' => $member->id])->assertRedirect();
    expect($admin->conversations()->count())->toBe(1);

    $this->actingAs($admin)->post(route('comments.store', $post), ['content' => 'Mag niet']);
    expect($post->comments()->count())->toBe(0);

    $this->actingAs($admin)->post(route('posts.like', $post));
    expect($post->likes()->count())->toBe(0);
});

test('members do not find admin accounts in the member search', function () {
    $member = User::factory()->create(['is_admin' => false, 'name' => 'Zoekbaar Lid']);
    User::factory()->create(['is_admin' => true, 'name' => 'Zoekbare Admin']);
    $searcher = User::factory()->create(['is_admin' => false]);

    $this->actingAs($searcher)->get(route('search', ['q' => 'Zoekb']))
        ->assertOk()
        ->assertSee($member->name)
        ->assertDontSee('Zoekbare Admin');
});

test('an admin search lists everybody alphabetically with their post count', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $bea = User::factory()->create(['is_admin' => false, 'name' => 'Bea', 'username' => null]);
    $ann = User::factory()->create(['is_admin' => false, 'name' => 'Ann', 'username' => null]);
    Post::factory()->count(2)->create(['user_id' => $ann->id]);

    $response = $this->actingAs($admin)->get(route('search'));

    $response->assertOk()->assertSee('2 posts');
    $body = $response->getContent();
    expect(strpos($body, 'Ann'))->toBeLessThan(strpos($body, $bea->name));
});

test('a reported post is hidden and the author gets a message with the review', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $author = User::factory()->create(['is_admin' => false]);
    $reader = User::factory()->create(['is_admin' => false]);
    $post = Post::factory()->create(['user_id' => $author->id, 'content' => 'Ongepaste post']);
    $reader->following()->attach($author->id);

    $this->actingAs($admin)->post(route('admin.posts.review', $post), ['reason' => 'Ongepast'])->assertRedirect();

    expect($post->fresh()->isUnderReview())->toBeTrue()
        ->and($author->notifications()->count())->toBe(1);

    $message = Message::where('system_type', Message::REVIEW)->firstOrFail();
    expect($message->user_id)->toBe($admin->id);

    $this->actingAs($reader)->get(route('explore'))->assertDontSee('Ongepaste post');
    $this->actingAs($reader)->get(route('home'))->assertDontSee('Ongepaste post');
    $this->actingAs($reader)->get(route('posts.show', $post))->assertForbidden();
    $this->actingAs($author)->get(route('posts.show', $post))->assertOk();
});

test('the author can agree with or contest the review from the conversation', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $author = User::factory()->create(['is_admin' => false]);
    $post = Post::factory()->create(['user_id' => $author->id]);

    $this->actingAs($admin)->post(route('admin.posts.review', $post));

    $conversation = $author->conversations()->firstOrFail();

    $this->actingAs($author)->get(route('messages.show', $conversation))
        ->assertOk()
        ->assertSee('I agree')
        ->assertSee('I contest');

    $this->actingAs($author)->post(route('messages.reply', $conversation), [
        'body' => 'I contest the review of my post.',
    ])->assertRedirect();

    expect($conversation->messages()->where('user_id', $author->id)->count())->toBe(1);
});

test('an admin sees the review list and can put a post back online', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $member = User::factory()->create(['is_admin' => false]);
    $post = Post::factory()->create(['user_id' => $member->id, 'content' => 'Post in review']);

    $this->actingAs($admin)->post(route('admin.posts.review', $post));
    $this->actingAs($admin)->get(route('admin.posts.index'))->assertOk()->assertSee('Post in review');

    $this->actingAs($member)->post(route('admin.posts.review', $post))->assertForbidden();

    $this->actingAs($admin)->delete(route('admin.posts.unreview', $post))->assertRedirect();
    expect($post->fresh()->isUnderReview())->toBeFalse();
});

test('a member is notified in the app when an admin answers the contact message', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $member = User::factory()->create(['is_admin' => false]);
    $contact = Contact::create([
        'name' => $member->name,
        'email' => $member->email,
        'type' => 'question',
        'subject' => 'Mijn vraag',
        'message' => 'Hoe werkt dit?',
    ]);

    $this->actingAs($admin)
        ->post(route('admin.contacts.reply', $contact), ['reply' => 'Zo werkt het.'])
        ->assertRedirect();

    $notification = $member->fresh()->notifications()->firstOrFail();

    expect($notification->data['url'])->toBe(route('contact.show', $contact));

    $this->actingAs($member)->get(route('contact.show', $contact))
        ->assertOk()
        ->assertSee('Zo werkt het.');

    $this->actingAs(User::factory()->create(['is_admin' => false]))
        ->get(route('contact.show', $contact))
        ->assertForbidden();
});
