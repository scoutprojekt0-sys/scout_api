<?php

namespace App\Policies;

use App\Models\Opportunity;
use App\Models\User;

class OpportunityPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Opportunity $opportunity): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->role === 'team';
    }

    public function update(User $user, Opportunity $opportunity): bool
    {
        return $user->id === $opportunity->team_user_id;
    }

    public function delete(User $user, Opportunity $opportunity): bool
    {
        return $user->id === $opportunity->team_user_id;
    }

    public function apply(User $user, Opportunity $opportunity): bool
    {
        return $user->role === 'player' && $opportunity->status === 'open';
    }
}
