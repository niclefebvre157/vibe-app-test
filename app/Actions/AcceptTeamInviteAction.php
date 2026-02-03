<?php

namespace App\Actions;

use App\Models\TeamInvite;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AcceptTeamInviteAction
{
    public function execute(TeamInvite $invite, User $user): void
    {
        if ($invite->accepted_at !== null) {
            abort(410, 'This invite has already been used.');
        }

        if ($invite->expires_at->isPast()) {
            abort(410, 'This invite has expired.');
        }

        DB::transaction(function () use ($invite, $user) {
            $invite->team->members()->attach($user->id, ['role' => 'player']);
            $invite->update(['accepted_at' => now()]);
        });
    }
}
