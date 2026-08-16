<?php

namespace App\Policies;

use App\Models\Contact;
use App\Models\User;

class ContactPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, Contact $contact): bool
    {
        return $user->isAdmin() || $user->email === $contact->email;
    }

    public function update(User $user, Contact $contact): bool
    {
        return $user->isAdmin();
    }
}
