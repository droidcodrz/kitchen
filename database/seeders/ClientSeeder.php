<?php

namespace Database\Seeders;

use App\Models\Client;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        $clients = [
            [
                'name' => 'Marrion Hotels',
                'contact_person' => 'Arjun Mehta',
                'email' => 'arjun@marrionhotels.com',
                'phone' => '+91-22-4567-8900',
                'address' => 'Marrion Tower, Marine Drive, Mumbai, Maharashtra 400020, India',
                'is_active' => true,
            ],
            [
                'name' => 'Cloud Kitchen Company',
                'contact_person' => 'Priya Sharma',
                'email' => 'priya@cloudkitchen.com',
                'phone' => '+91-11-3456-7890',
                'address' => '45, Cyber Hub, Gurgaon, Haryana 122001, India',
                'is_active' => true,
            ],
            [
                'name' => 'Grand Palace Hotels',
                'contact_person' => 'Rajesh Kumar',
                'email' => 'rajesh@grandpalace.com',
                'phone' => '+91-80-2345-6789',
                'address' => 'Palace Road, Whitefield, Bangalore, Karnataka 560066, India',
                'is_active' => true,
            ],
            [
                'name' => 'Fresh Foods Corporation',
                'contact_person' => 'Neha Gupta',
                'email' => 'neha@freshfoods.com',
                'phone' => '+91-20-8765-4321',
                'address' => '12, Food Park, Pune, Maharashtra 411045, India',
                'is_active' => true,
            ],
            [
                'name' => 'Restaurant Chain India',
                'contact_person' => 'Vikram Patel',
                'email' => 'vikram@rciventures.com',
                'phone' => '+91-79-2234-5678',
                'address' => '78, Business District, Ahmedabad, Gujarat 380001, India',
                'is_active' => true,
            ],
        ];

        foreach ($clients as $client) {
            Client::create($client);
        }
    }
}
