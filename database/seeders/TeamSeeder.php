<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TeamSeeder extends Seeder
{
    public function run(): void
    {
        $memberRole = Role::where('slug', 'team-member')->first();
        $managerRole = Role::where('slug', 'project-manager')->first();

        // Create some team members first
        $users = [
            [
                'first_name' => 'Rajesh',
                'last_name' => 'Kumar',
                'email' => 'rajesh.kumar@company.com',
                'phone_number' => '+91-98765-43210',
                'password' => Hash::make('password'),
                'role_id' => $memberRole->id,
            ],
            [
                'first_name' => 'Priya',
                'last_name' => 'Sharma',
                'email' => 'priya.sharma@company.com',
                'phone_number' => '+91-98765-43211',
                'password' => Hash::make('password'),
                'role_id' => $memberRole->id,
            ],
            [
                'first_name' => 'Amit',
                'last_name' => 'Patel',
                'email' => 'amit.patel@company.com',
                'phone_number' => '+91-98765-43212',
                'password' => Hash::make('password'),
                'role_id' => $memberRole->id,
            ],
            [
                'first_name' => 'Neha',
                'last_name' => 'Gupta',
                'email' => 'neha.gupta@company.com',
                'phone_number' => '+91-98765-43213',
                'password' => Hash::make('password'),
                'role_id' => $memberRole->id,
            ],
            [
                'first_name' => 'Vikram',
                'last_name' => 'Singh',
                'email' => 'vikram.singh@company.com',
                'phone_number' => '+91-98765-43214',
                'password' => Hash::make('password'),
                'role_id' => $memberRole->id,
            ],
            [
                'first_name' => 'Anjali',
                'last_name' => 'Reddy',
                'email' => 'anjali.reddy@company.com',
                'phone_number' => '+91-98765-43215',
                'password' => Hash::make('password'),
                'role_id' => $memberRole->id,
            ],
            [
                'first_name' => 'Arjun',
                'last_name' => 'Mehta',
                'email' => 'arjun.mehta@company.com',
                'phone_number' => '+91-98765-43216',
                'password' => Hash::make('password'),
                'role_id' => $managerRole->id,
            ],
            [
                'first_name' => 'Kavita',
                'last_name' => 'Desai',
                'email' => 'kavita.desai@company.com',
                'phone_number' => '+91-98765-43217',
                'password' => Hash::make('password'),
                'role_id' => $managerRole->id,
            ],
            [
                'first_name' => 'Rohit',
                'last_name' => 'Verma',
                'email' => 'rohit.verma@company.com',
                'phone_number' => '+91-98765-43218',
                'password' => Hash::make('password'),
                'role_id' => $memberRole->id,
            ],
            [
                'first_name' => 'Sanjay',
                'last_name' => 'Kumar',
                'email' => 'sanjay.kumar@company.com',
                'phone_number' => '+91-98765-43219',
                'password' => Hash::make('password'),
                'role_id' => $memberRole->id,
            ],
        ];

        $createdUsers = [];
        foreach ($users as $userData) {
            $createdUsers[] = User::firstOrCreate(['email' => $userData['email']], $userData);
        }

        // Create teams
        $teams = [
            [
                'name' => 'Team Alpha',
                'slug' => 'team-alpha',
                'description' => 'Primary production team for commercial refrigeration',
                'is_active' => true,
                'member_ids' => [0, 1, 2], // Rajesh, Priya, Amit
            ],
            [
                'name' => 'Team Beta',
                'slug' => 'team-beta',
                'description' => 'Specialized team for cooking equipment manufacturing',
                'is_active' => true,
                'member_ids' => [3, 4, 5], // Neha, Vikram, Anjali
            ],
            [
                'name' => 'Team Gamma',
                'slug' => 'team-gamma',
                'description' => 'Quality control and assembly verification team',
                'is_active' => true,
                'member_ids' => [6, 7, 8], // Arjun, Kavita, Rohit
            ],
            [
                'name' => 'Team Delta',
                'slug' => 'team-delta',
                'description' => 'Custom project implementation and client support',
                'is_active' => true,
                'member_ids' => [1, 4, 9], // Priya, Vikram, Sanjay
            ],
        ];

        foreach ($teams as $teamData) {
            $memberIds = $teamData['member_ids'];
            unset($teamData['member_ids']);

            $team = Team::firstOrCreate(['slug' => $teamData['slug']], $teamData);

            // Attach members to team
            foreach ($memberIds as $index) {
                if (isset($createdUsers[$index])) {
                    $team->users()->syncWithoutDetaching([$createdUsers[$index]->id]);
                }
            }
        }
    }
}
