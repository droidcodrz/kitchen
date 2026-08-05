<?php

namespace App\Http\Controllers;

use App\Http\Requests\Team\StoreTeamRequest;
use App\Http\Requests\Team\UpdateTeamRequest;
use App\Models\Role;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class TeamController extends Controller
{
    /**
     * Display a listing of teams.
     */
    public function index(Request $request): View
    {
        $teams = Team::with(['users.role', 'projects'])
            ->withCount(['users', 'projects'])
            ->latest()
            ->paginate(15);

        $roles = Role::all();

        if ($request->ajax()) {
            return view('teams._list', compact('teams'));
        }

        return view('teams.index', compact('teams', 'roles'));
    }

    /**
     * Show the form for creating a new team.
     */
    public function create(): View
    {
        $users = User::where('status', 'active')
            ->orderBy('first_name')
            ->get();

        return view('teams.create', compact('users'));
    }

    /**
     * Store a newly created team.
     */
    public function store(StoreTeamRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['slug'] = Str::slug($data['name']);

        $team = Team::create($data);

        // Attach members if provided
        if ($request->has('members')) {
            $team->users()->attach($request->input('members'));
        }

        return redirect()->route('teams.index')
            ->with('success', 'Team created successfully.');
    }

    /**
     * Display the specified team.
     */
    public function show(Team $team): View
    {
        $team->load(['users.role', 'projects.client']);

        // Get users not already in this team for the add member form
        $availableUsers = User::where('status', 'active')
            ->whereNotIn('id', $team->users->pluck('id'))
            ->orderBy('first_name')
            ->get();

        return view('teams.show', compact('team', 'availableUsers'));
    }

    /**
     * Show the form for editing the specified team.
     */
    public function edit(Team $team): View
    {
        return view('teams.edit', compact('team'));
    }

    /**
     * Update the specified team.
     */
    public function update(UpdateTeamRequest $request, Team $team): RedirectResponse
    {
        $data = $request->validated();
        $data['slug'] = Str::slug($data['name']);

        $team->update($data);

        return redirect()->route('teams.show', $team)
            ->with('success', 'Team updated successfully.');
    }

    /**
     * Remove the specified team (soft delete).
     */
    public function destroy(Team $team): RedirectResponse
    {
        $team->delete();

        return redirect()->route('teams.index')
            ->with('success', 'Team deleted successfully.');
    }
}
