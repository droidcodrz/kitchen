<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(): View
    {
        $users = User::with('role')
            ->latest()
            ->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create(): View
    {
        $roles = Role::all();

        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store a newly created user.
     */
    public function store(StoreUserRequest $request): RedirectResponse|JsonResponse
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);
        $teamId = $data['team_id'] ?? null;
        unset($data['team_id']);

        $user = User::create($data);

        if ($teamId) {
            $user->teams()->attach($teamId, ['joined_at' => now()]);

            if ($request->wantsJson()) {
                return response()->json(['message' => 'Team member added successfully.']);
            }

            return redirect()->route('teams.index')
                ->with('success', 'Team member added successfully.');
        }

        if ($request->wantsJson()) {
            return response()->json(['message' => 'User created successfully.']);
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }

    /**
     * Display the specified user.
     */
    public function show(User $user): View
    {
        $user->load(['role', 'teams', 'projects']);

        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user): View
    {
        $roles = Role::all();

        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified user.
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()->route('admin.users.show', $user)
            ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user (soft delete).
     */
    public function destroy(User $user): RedirectResponse
    {
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }
}
