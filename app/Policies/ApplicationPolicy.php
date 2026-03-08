<?php

namespace App\Policies;

use App\Models\Application;
use App\Models\User;

class ApplicationPolicy
{
    public function view(User $user, Application $application): bool
    {
        return $user->id === $application->player_user_id
            || $user->id === $application->opportunity->team_user_id;
    }

    public function changeStatus(User $user, Application $application): bool
    {
        return $user->id === $application->opportunity->team_user_id;
    }
}
