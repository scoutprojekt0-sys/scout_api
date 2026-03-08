<?php

namespace App\Policies;

use App\Models\Contact;
use App\Models\User;

class ContactPolicy
{
    public function view(User $user, Contact $contact): bool
    {
        return $user->id === $contact->from_user_id || $user->id === $contact->to_user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function updateStatus(User $user, Contact $contact): bool
    {
        return $user->id === $contact->to_user_id;
    }
}
