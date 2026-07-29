<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define which permissions each role should have
        $rolePermissions = [
            'admin' => $this->getAllPermissionSlugs(),

            'project-manager' => [
                // All project permissions
                'view-projects',
                'create-projects',
                'edit-projects',
                'delete-projects',
                'manage-project-status',
                // All product permissions
                'view-products',
                'create-products',
                'edit-products',
                'delete-products',
                // Inventory: view, create, edit
                'view-inventory',
                'create-inventory',
                'edit-inventory',
                // Teams: view only
                'view-teams',
                // All report permissions
                'view-reports',
                'export-reports',
            ],

            'team-lead' => [
                'view-projects',
                'view-products',
                'view-inventory',
                'view-teams',
                'manage-project-status',
            ],

            'team-member' => [
                'view-projects',
                'view-products',
                'view-teams',
            ],
        ];

        foreach ($rolePermissions as $roleSlug => $permissionSlugs) {
            $role = DB::table('roles')->where('slug', $roleSlug)->first();

            if (! $role) {
                $this->command->warn("Role '{$roleSlug}' not found. Skipping.");
                continue;
            }

            $permissionIds = DB::table('permissions')
                ->whereIn('slug', $permissionSlugs)
                ->pluck('id')
                ->toArray();

            // Remove existing entries for this role to avoid duplicates
            DB::table('permission_role')->where('role_id', $role->id)->delete();

            $inserts = [];
            foreach ($permissionIds as $permissionId) {
                $inserts[] = [
                    'role_id'       => $role->id,
                    'permission_id' => $permissionId,
                ];
            }

            if (! empty($inserts)) {
                DB::table('permission_role')->insert($inserts);
            }
        }
    }

    /**
     * Get all permission slugs defined in the system.
     */
    private function getAllPermissionSlugs(): array
    {
        return [
            // Projects
            'view-projects',
            'create-projects',
            'edit-projects',
            'delete-projects',
            'manage-project-status',
            // Products
            'view-products',
            'create-products',
            'edit-products',
            'delete-products',
            // Inventory
            'view-inventory',
            'create-inventory',
            'edit-inventory',
            'delete-inventory',
            'adjust-stock',
            // Teams
            'view-teams',
            'create-teams',
            'edit-teams',
            'delete-teams',
            'manage-team-members',
            // Users
            'view-users',
            'create-users',
            'edit-users',
            'delete-users',
            // Settings
            'manage-settings',
            'manage-alerts',
            // Reports
            'view-reports',
            'export-reports',
        ];
    }
}
