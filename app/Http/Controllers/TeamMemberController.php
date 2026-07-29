<?php

namespace App\Http\Controllers;

use App\Http\Requests\Team\AddTeamMemberRequest;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\RedirectResponse;

class TeamMemberController extends Controller
{
    /**
     * Add a user to the specified team.
     */
    public function store(AddTeamMemberRequest $request, Team $team): RedirectResponse
    {
        $team->users()->attach($request->validated('user_id'), [
            'role_in_team' => $request->validated('role_in_team', 'member'),
            'joined_at' => now(),
        ]);

        return redirect()->back()
            ->with('success', 'Team member added successfully.');
    }

    /**
     * Remove a user from the specified team.
     */
    public function destroy(Team $team, User $user): RedirectResponse
    {
        $team->users()->detach($user->id);

        return redirect()->back()
            ->with('success', 'Team member removed successfully.');
    }
}
