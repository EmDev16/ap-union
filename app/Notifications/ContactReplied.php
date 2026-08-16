<?php

namespace App\Notifications;

use App\Models\Contact;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ContactReplied extends Notification
{
    use Queueable;

    public function __construct(public Contact $contact) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Answer to your message',
            'description' => 'An admin answered your message "'.$this->contact->subject.'".',
            'url' => route('contact.show', $this->contact),
        ];
    }
}
