<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        $roles = [
            [
                'name'        => 'Admin',
                'slug'        => 'admin',
                'description' => 'Full system access',
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'name'        => 'Project Manager',
                'slug'        => 'project-manager',
                'description' => 'Manages projects, products, inventory',
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'name'        => 'Team Lead',
                'slug'        => 'team-lead',
                'description' => 'Leads teams, views assigned projects',
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'name'        => 'Team Member',
                'slug'        => 'team-member',
                'description' => 'Views assigned projects',
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->updateOrInsert(
                ['slug' => $role['slug']],
                $role
            );
        }
    }
}
