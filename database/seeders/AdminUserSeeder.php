<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = DB::table('roles')->where('slug', 'admin')->first();

        if (! $adminRole) {
            $this->command->error('Admin role not found. Please run RolesSeeder first.');
            return;
        }

        $now = Carbon::now();

        DB::table('users')->updateOrInsert(
            ['email' => 'admin@kitchen.com'],
            [
                'first_name'        => 'Admin',
                'last_name'         => 'User',
                'email'             => 'admin@kitchen.com',
                'password'          => Hash::make('password'),
                'role_id'           => $adminRole->id,
                'status'            => 'active',
                'email_verified_at' => $now,
                'created_at'        => $now,
                'updated_at'        => $now,
            ]
        );
    }
}
