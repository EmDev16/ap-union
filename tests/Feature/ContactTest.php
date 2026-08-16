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

test('the contact email is sent to the configured admin address', function () {
    Mail::fake();
    config(['mail.admin_address' => 'beheer@apunion.test']);

    $this->post(route('contact.store'), [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'subject' => 'Samenwerking',
        'message' => 'Graag meer info.',
    ]);

    Mail::assertSent(ContactReceived::class, fn (ContactReceived $mail) => $mail->hasTo('beheer@apunion.test')
        && $mail->envelope()->subject === 'Nieuw contactbericht: Samenwerking');
});
