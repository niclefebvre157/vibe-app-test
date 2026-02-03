<?php

namespace App\Actions;

use App\Mail\TeamInviteMail;
use App\Models\Team;
use App\Models\TeamInvite;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class SendTeamInviteAction
{
    public function execute(Team $team, User $inviter, string $email): TeamInvite
    {
        if ($team->members()->where('email', $email)->exists()) {
            throw ValidationException::withMessages([
                'email' => 'This user is already a member of the team.',
            ]);
        }

        if ($team->invites()->pending()->where('email', $email)->exists()) {
            throw ValidationException::withMessages([
                'email' => 'A pending invite already exists for this email.',
            ]);
        }

        $invite = TeamInvite::create([
            'team_id' => $team->id,
            'invited_by' => $inviter->id,
            'email' => $email,
            'token' => Str::random(64),
            'expires_at' => now()->addDays(7),
        ]);

        Mail::to($email)->send(new TeamInviteMail($invite));

        return $invite;
    }
}
