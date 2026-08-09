<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Metals',
                'slug' => 'metals',
                'description' => 'Sheet metal, angle bars, and metal accessories',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Tubes & Pipes',
                'slug' => 'tubes-pipes',
                'description' => 'Steel tubes (stainless & black iron), pipes with various gauges and diameters',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Valves & Fittings',
                'slug' => 'valves-fittings',
                'description' => 'Gas valves, water valves, burners, and pipe fitters',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Consumables',
                'slug' => 'consumables',
                'description' => 'Nozzles, torch tips, wires, electrodes, welding sticks',
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'Chemicals',
                'slug' => 'chemicals',
                'description' => 'Paint, glue, machine grease, and other chemical products',
                'sort_order' => 5,
                'is_active' => true,
            ],
            [
                'name' => 'Gases',
                'slug' => 'gases',
                'description' => 'Oxygen, nitrogen, propane gas cylinders',
                'sort_order' => 6,
                'is_active' => true,
            ],
            [
                'name' => 'Custom Fabrication',
                'slug' => 'custom-fabrication',
                'description' => 'CNC laser cut parts and custom metal fabrication',
                'sort_order' => 7,
                'is_active' => true,
            ],
            [
                'name' => 'Commercial Kitchen Equipment',
                'slug' => 'commercial-kitchen-equipment',
                'description' => 'Large-scale kitchen equipment for commercial use',
                'sort_order' => 10,
                'is_active' => true,
            ],
            [
                'name' => 'Refrigeration',
                'slug' => 'refrigeration',
                'description' => 'Commercial refrigerators and freezers',
                'sort_order' => 11,
                'is_active' => true,
            ],
            [
                'name' => 'Cooking Equipment',
                'slug' => 'cooking-equipment',
                'description' => 'Stoves, ovens, grills, and cooking appliances',
                'sort_order' => 12,
                'is_active' => true,
            ],
            [
                'name' => 'Ventilation',
                'slug' => 'ventilation',
                'description' => 'Exhaust hoods and ventilation systems',
                'sort_order' => 13,
                'is_active' => true,
            ],
            [
                'name' => 'Food Preparation',
                'slug' => 'food-preparation',
                'description' => 'Mixers, food processors, and other food prep equipment',
                'sort_order' => 14,
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }
    }
}
