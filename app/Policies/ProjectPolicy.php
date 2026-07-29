<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $this->hasPermission($user, 'view-projects');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Project $project): bool
    {
        return $this->hasPermission($user, 'view-projects');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $this->hasPermission($user, 'create-projects');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Project $project): bool
    {
        // Check user permission first
        if (!$this->hasPermission($user, 'edit-projects')) {
            return false;
        }

        // Only allow editing before production starts
        // Once in production, specs are locked to prevent errors and material waste
        $editableStatuses = ['draft', 'confirmed'];

        return in_array($project->status, $editableStatuses);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Project $project): bool
    {
        // Check user permission first
        if (!$this->hasPermission($user, 'delete-projects')) {
            return false;
        }

        // Only allow deletion of draft and confirmed projects
        // Once in production or beyond, maintain complete audit trail
        $deletableStatuses = ['draft', 'confirmed'];

        return in_array($project->status, $deletableStatuses);
    }

    /**
     * Determine whether the user can change the project status.
     */
    public function changeStatus(User $user, Project $project): bool
    {
        return $this->hasPermission($user, 'manage-project-status');
    }

    /**
     * Check if the user has a specific permission through their role.
     */
    private function hasPermission(User $user, string $permission): bool
    {
        return $user->role?->permissions()->where('slug', $permission)->exists() ?? false;
    }
}
