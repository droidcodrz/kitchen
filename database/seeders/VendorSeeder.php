<?php

namespace Database\Seeders;

use App\Models\Vendor;
use Illuminate\Database\Seeder;

class VendorSeeder extends Seeder
{
    public function run(): void
    {
        $vendors = [
            [
                'name' => 'Steel Industries Ltd',
                'contact_person' => 'Rajesh Kumar',
                'email' => 'rajesh@steelindustries.com',
                'phone' => '+91-98765-43210',
                'address' => 'Plot 45, Industrial Area, Phase 2, Mumbai, Maharashtra 400001, India',
                'is_active' => true,
            ],
            [
                'name' => 'Premium Parts Suppliers',
                'contact_person' => 'Amit Sharma',
                'email' => 'amit@premiumparts.com',
                'phone' => '+91-98765-43211',
                'address' => '12, Industrial Zone, Sector 5, New Delhi 110001, India',
                'is_active' => true,
            ],
            [
                'name' => 'Global Components Inc',
                'contact_person' => 'Priya Patel',
                'email' => 'priya@globalcomponents.com',
                'phone' => '+91-98765-43212',
                'address' => '78, Export Zone, Bangalore, Karnataka 560001, India',
                'is_active' => true,
            ],
            [
                'name' => 'Tech Hardware Solutions',
                'contact_person' => 'Vikram Singh',
                'email' => 'vikram@techhardware.com',
                'phone' => '+91-98765-43213',
                'address' => '34, Tech Park, Pune, Maharashtra 411001, India',
                'is_active' => true,
            ],
        ];

        foreach ($vendors as $vendor) {
            Vendor::firstOrCreate(['email' => $vendor['email']], $vendor);
        }
    }
}
