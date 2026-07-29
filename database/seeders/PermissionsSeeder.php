<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        $permissionsByModule = [
            'projects' => [
                'view-projects',
                'create-projects',
                'edit-projects',
                'delete-projects',
                'manage-project-status',
            ],
            'products' => [
                'view-products',
                'create-products',
                'edit-products',
                'delete-products',
            ],
            'inventory' => [
                'view-inventory',
                'create-inventory',
                'edit-inventory',
                'delete-inventory',
                'adjust-stock',
            ],
            'teams' => [
                'view-teams',
                'create-teams',
                'edit-teams',
                'delete-teams',
                'manage-team-members',
            ],
            'users' => [
                'view-users',
                'create-users',
                'edit-users',
                'delete-users',
            ],
            'settings' => [
                'manage-settings',
                'manage-alerts',
            ],
            'reports' => [
                'view-reports',
                'export-reports',
            ],
        ];

        foreach ($permissionsByModule as $module => $permissions) {
            foreach ($permissions as $permissionSlug) {
                $name = ucwords(str_replace('-', ' ', $permissionSlug));

                DB::table('permissions')->updateOrInsert(
                    ['slug' => $permissionSlug],
                    [
                        'name'       => $name,
                        'slug'       => $permissionSlug,
                        'group_name' => $module,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]
                );
            }
        }
    }
}
