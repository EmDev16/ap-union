<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\RedirectResponse;
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
}
