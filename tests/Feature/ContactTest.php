<?php

use App\Mail\ContactReceived;
use App\Models\Contact;
use Illuminate\Support\Facades\Mail;

test('guests can submit a contact message and the admin receives an email', function () {
    Mail::fake();

    $this->post(route('contact.store'), [
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'subject' => 'Vraag over AP Union',
        'message' => 'Ik heb een vraag over het platform.',
    ])->assertSessionHas('status');

    $contact = Contact::where('email', 'jane@example.com')->firstOrFail();
    Mail::assertSent(ContactReceived::class, fn (ContactReceived $mail) => $mail->contact->is($contact));
});
