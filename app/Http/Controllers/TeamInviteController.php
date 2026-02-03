<?php

namespace App\Http\Controllers;

use App\Actions\AcceptTeamInviteAction;
use App\Actions\SendTeamInviteAction;
use App\Http\Requests\StoreTeamInviteRequest;
use App\Models\Team;
use App\Models\TeamInvite;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TeamInviteController extends Controller
{
    public function create(Team $team): View
    {
        $this->authorize('invite', $team);

        return view('teams.invites.create', compact('team'));
    }

    public function store(StoreTeamInviteRequest $request, Team $team, SendTeamInviteAction $action): RedirectResponse
    {
        $action->execute($team, $request->user(), $request->validated('email'));

        return redirect()->route('teams.show', $team)->with('success', 'Invitation sent.');
    }

    public function accept(string $token, AcceptTeamInviteAction $action): RedirectResponse
    {
        $invite = TeamInvite::where('token', $token)->firstOrFail();

        if (! Auth::check()) {
            redirect()->setIntendedUrl(url("/invites/{$token}/accept"));

            return redirect()->route('login');
        }

        $action->execute($invite, Auth::user());

        return redirect()->route('teams.show', $invite->team)->with('success', 'You have joined the team.');
    }
}
