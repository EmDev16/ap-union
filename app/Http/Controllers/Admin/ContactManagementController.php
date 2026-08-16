<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\ContactAnswered;
use App\Models\Contact;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class ContactManagementController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', Contact::class);

        return view('admin.contacts.index', ['contacts' => Contact::latest()->get()]);
    }

    public function update(Contact $contact): RedirectResponse
    {
        $this->authorize('update', $contact);
        $contact->update(['is_answered' => true]);

        return to_route('admin.contacts.index')->with('status', 'Bericht gemarkeerd als beantwoord.');
    }

    /**
     * Answer a contact message by mail from the admin panel.
     */
    public function reply(Request $request, Contact $contact): RedirectResponse
    {
        $this->authorize('update', $contact);

        $validated = $request->validate([
            'reply' => ['required', 'string', 'max:2000'],
        ]);

        $contact->update([
            'reply' => $validated['reply'],
            'replied_at' => now(),
            'is_answered' => true,
        ]);

        Mail::to($contact->email)->send(new ContactAnswered($contact));

        return back()->with('status', 'Antwoord verstuurd.');
    }
}
