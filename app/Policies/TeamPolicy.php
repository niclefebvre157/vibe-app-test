<?php

namespace App\Policies;

use App\Models\Team;
use App\Models\User;

class TeamPolicy
{
    public function view(User $user, Team $team): bool
    {
        return $team->members()->where('user_id', $user->id)->exists();
    }

    public function invite(User $user, Team $team): bool
    {
        return $team->memberships()
            ->where('user_id', $user->id)
            ->where('role', 'admin')
            ->exists();
    }
}
