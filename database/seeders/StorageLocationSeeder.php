<?php

namespace Database\Seeders;

use App\Models\StorageLocation;
use Illuminate\Database\Seeder;

class StorageLocationSeeder extends Seeder
{
    public function run(): void
    {
        $locations = [
            [
                'name' => 'Main Warehouse',
                'code' => 'WH-MAIN',
                'description' => 'Primary warehouse for raw materials and finished products',
                'is_active' => true,
            ],
            [
                'name' => 'Assembly Floor A',
                'code' => 'ASM-A',
                'description' => 'Production floor for refrigeration equipment assembly',
                'is_active' => true,
            ],
            [
                'name' => 'Assembly Floor B',
                'code' => 'ASM-B',
                'description' => 'Production floor for cooking equipment assembly',
                'is_active' => true,
            ],
            [
                'name' => 'Cold Storage Unit',
                'code' => 'CS-01',
                'description' => 'Temperature-controlled storage for sensitive components',
                'is_active' => true,
            ],
            [
                'name' => 'Quality Control Lab',
                'code' => 'QC-LAB',
                'description' => 'Quality control and testing facility',
                'is_active' => true,
            ],
        ];

        foreach ($locations as $location) {
            StorageLocation::create($location);
        }
    }
}
