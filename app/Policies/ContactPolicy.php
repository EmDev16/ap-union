<?php

namespace App\Policies;

use App\Models\Contact;
use App\Models\User;

class ContactPolicy
{
    public function viewAny(User $user): bool { return $user->is_admin; }
    public function update(User $user, Contact $contact): bool { return $user->is_admin; }
}
