<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewProfile(User $user, User $targetUser): bool
    {
        return true;
    }

    public function update(User $user, User $targetUser): bool
    {
        return $user->id === $targetUser->id;
    }

    public function delete(User $user, User $targetUser): bool
    {
        return $user->id === $targetUser->id;
    }
}
