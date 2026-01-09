<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use App\Mail\ContactReceived;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string',
        ]);

        $message = ContactMessage::create($data);

        // Mail naar admin
        Mail::to(config('mail.admin_address', 'admin@ehb.be'))
            ->send(new ContactReceived($message));

        return back()->with('success', 'Bericht verstuurd');
    }
}
