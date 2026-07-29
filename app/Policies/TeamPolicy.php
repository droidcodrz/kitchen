<?php

namespace App\Policies;

use App\Models\Team;
use App\Models\User;

class TeamPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $this->hasPermission($user, 'view-teams');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Team $team): bool
    {
        return $this->hasPermission($user, 'view-teams');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $this->hasPermission($user, 'create-teams');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Team $team): bool
    {
        return $this->hasPermission($user, 'edit-teams');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Team $team): bool
    {
        return $this->hasPermission($user, 'delete-teams');
    }

    /**
     * Determine whether the user can manage members of this team.
     */
    public function manageMember(User $user, Team $team): bool
    {
        return $this->hasPermission($user, 'manage-team-members');
    }

    /**
     * Check if the user has a specific permission through their role.
     */
    private function hasPermission(User $user, string $permission): bool
    {
        return $user->role?->permissions()->where('slug', $permission)->exists() ?? false;
    }
}
