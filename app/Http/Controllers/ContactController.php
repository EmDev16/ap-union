<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactRequest;
use App\Mail\ContactReceived;
use App\Models\Contact;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function create(): View
    {
        return view('contact');
    }

    public function show(Contact $contact): View
    {
        $this->authorize('view', $contact);

        return view('contact-answer', ['contact' => $contact]);
    }

    public function store(StoreContactRequest $request): RedirectResponse
    {
        $contact = Contact::create($request->validated());
        Mail::to(config('mail.admin_address'))->send(new ContactReceived($contact));

        return back()->with('status', 'Bedankt! Je bericht is verstuurd.');
    }
}
