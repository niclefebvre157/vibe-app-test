<?php

namespace App\Actions;

use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CreateTeamAction
{
    public function execute(User $user, array $data): Team
    {
        return DB::transaction(function () use ($user, $data) {
            $team = Team::create($data);

            $team->members()->attach($user->id, ['role' => 'admin']);

            return $team;
        });
    }
}
