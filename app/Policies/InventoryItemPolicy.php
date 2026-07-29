<?php

namespace App\Policies;

use App\Models\InventoryItem;
use App\Models\User;

class InventoryItemPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $this->hasPermission($user, 'view-inventory');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, InventoryItem $inventoryItem): bool
    {
        return $this->hasPermission($user, 'view-inventory');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $this->hasPermission($user, 'create-inventory');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, InventoryItem $inventoryItem): bool
    {
        return $this->hasPermission($user, 'edit-inventory');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, InventoryItem $inventoryItem): bool
    {
        return $this->hasPermission($user, 'delete-inventory');
    }

    /**
     * Determine whether the user can adjust stock for this item.
     */
    public function adjustStock(User $user, InventoryItem $inventoryItem): bool
    {
        return $this->hasPermission($user, 'adjust-stock');
    }

    /**
     * Check if the user has a specific permission through their role.
     */
    private function hasPermission(User $user, string $permission): bool
    {
        return $user->role?->permissions()->where('slug', $permission)->exists() ?? false;
    }
}
