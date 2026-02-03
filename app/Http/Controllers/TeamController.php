<?php

namespace App\Http\Controllers;

use App\Actions\CreateTeamAction;
use App\Http\Requests\StoreTeamRequest;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TeamController extends Controller
{
    public function index(): View
    {
        $teams = auth()->user()->teams;

        return view('teams.index', compact('teams'));
    }

    public function create(): View
    {
        return view('teams.create');
    }

    public function store(StoreTeamRequest $request, CreateTeamAction $action): RedirectResponse
    {
        $team = $action->execute($request->user(), $request->validated());

        return redirect()->route('teams.show', $team);
    }

    public function show(Team $team): View
    {
        $this->authorize('view', $team);

        $team->load('members');

        return view('teams.show', compact('team'));
    }
}
