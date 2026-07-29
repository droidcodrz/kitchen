<?php

namespace Database\Seeders;

use App\Models\DropdownOption;
use Illuminate\Database\Seeder;

class DropdownOptionSeeder extends Seeder
{
    public function run(): void
    {
        $options = [
            ['type' => 'item_type', 'value' => 'Sheet Metal', 'label' => 'Sheet Metal', 'sort_order' => 1],
            ['type' => 'item_type', 'value' => 'Angle Bar', 'label' => 'Angle Bar', 'sort_order' => 2],
            ['type' => 'item_type', 'value' => 'Steel Tube', 'label' => 'Steel Tube', 'sort_order' => 3],
            ['type' => 'item_type', 'value' => 'Gas Valve', 'label' => 'Gas Valve', 'sort_order' => 4],
            ['type' => 'item_type', 'value' => 'Water Valve', 'label' => 'Water Valve', 'sort_order' => 5],
            ['type' => 'item_type', 'value' => 'Burner', 'label' => 'Burner', 'sort_order' => 6],
            ['type' => 'item_type', 'value' => 'Pipe Fitter', 'label' => 'Pipe Fitter', 'sort_order' => 7],
            ['type' => 'item_type', 'value' => 'Metal Accessory', 'label' => 'Metal Accessory', 'sort_order' => 8],
            ['type' => 'item_type', 'value' => 'Restaurant Fan', 'label' => 'Restaurant Fan', 'sort_order' => 9],
            ['type' => 'item_type', 'value' => 'Food Truck Fan', 'label' => 'Food Truck Fan', 'sort_order' => 10],
            ['type' => 'item_type', 'value' => 'CNC Laser Cut', 'label' => 'CNC Laser Cut', 'sort_order' => 11],
            ['type' => 'item_type', 'value' => 'Miscellaneous', 'label' => 'Miscellaneous', 'sort_order' => 99],

            ['type' => 'material_type', 'value' => 'Stainless Steel', 'label' => 'Stainless Steel', 'sort_order' => 1],
            ['type' => 'material_type', 'value' => 'Black Iron', 'label' => 'Black Iron', 'sort_order' => 2],
            ['type' => 'material_type', 'value' => 'Aluminum', 'label' => 'Aluminum', 'sort_order' => 3],
            ['type' => 'material_type', 'value' => 'Galvanize Steel', 'label' => 'Galvanize Steel', 'sort_order' => 4],
            ['type' => 'material_type', 'value' => 'Silicon', 'label' => 'Silicon', 'sort_order' => 5],
            ['type' => 'material_type', 'value' => 'Fire Wrap', 'label' => 'Fire Wrap', 'sort_order' => 6],
            ['type' => 'material_type', 'value' => 'Paint', 'label' => 'Paint', 'sort_order' => 7],
            ['type' => 'material_type', 'value' => 'Glue', 'label' => 'Glue', 'sort_order' => 8],
            ['type' => 'material_type', 'value' => 'Nozzle', 'label' => 'Nozzle', 'sort_order' => 9],
            ['type' => 'material_type', 'value' => 'Torch Tip', 'label' => 'Torch Tip', 'sort_order' => 10],
            ['type' => 'material_type', 'value' => 'Wire', 'label' => 'Wire', 'sort_order' => 11],
            ['type' => 'material_type', 'value' => 'Electrode', 'label' => 'Electrode', 'sort_order' => 12],
            ['type' => 'material_type', 'value' => 'Welding Stick', 'label' => 'Welding Stick', 'sort_order' => 13],
            ['type' => 'material_type', 'value' => 'Oxygen', 'label' => 'Oxygen', 'sort_order' => 14],
            ['type' => 'material_type', 'value' => 'Nitrogen', 'label' => 'Nitrogen', 'sort_order' => 15],
            ['type' => 'material_type', 'value' => 'Propane', 'label' => 'Propane', 'sort_order' => 16],
            ['type' => 'material_type', 'value' => 'Machine Grease', 'label' => 'Machine Grease', 'sort_order' => 17],

            ['type' => 'material_grade', 'value' => '304', 'label' => '304', 'sort_order' => 1],
            ['type' => 'material_grade', 'value' => '430', 'label' => '430', 'sort_order' => 2],
            ['type' => 'material_grade', 'value' => '316', 'label' => '316', 'sort_order' => 3],
            ['type' => 'material_grade', 'value' => '201', 'label' => '201', 'sort_order' => 4],

            ['type' => 'thickness_gauge', 'value' => '11', 'label' => '11', 'sort_order' => 1],
            ['type' => 'thickness_gauge', 'value' => '14', 'label' => '14', 'sort_order' => 2],
            ['type' => 'thickness_gauge', 'value' => '16', 'label' => '16', 'sort_order' => 3],
            ['type' => 'thickness_gauge', 'value' => '18', 'label' => '18', 'sort_order' => 4],
            ['type' => 'thickness_gauge', 'value' => '20', 'label' => '20', 'sort_order' => 5],
            ['type' => 'thickness_gauge', 'value' => '22', 'label' => '22', 'sort_order' => 6],
            ['type' => 'thickness_gauge', 'value' => '24', 'label' => '24', 'sort_order' => 7],
            ['type' => 'thickness_gauge', 'value' => '26', 'label' => '26', 'sort_order' => 8],

            ['type' => 'inventory_type', 'value' => 'Raw Materials', 'label' => 'Raw Materials', 'sort_order' => 1],
            ['type' => 'inventory_type', 'value' => 'Consumables', 'label' => 'Consumables', 'sort_order' => 2],
            ['type' => 'inventory_type', 'value' => 'Parts & Components', 'label' => 'Parts & Components', 'sort_order' => 3],
            ['type' => 'inventory_type', 'value' => 'Finished Goods', 'label' => 'Finished Goods', 'sort_order' => 4],
            ['type' => 'inventory_type', 'value' => 'Merchandise Goods', 'label' => 'Merchandise Goods', 'sort_order' => 5],

            ['type' => 'system_category', 'value' => 'metals', 'label' => 'Metals', 'sort_order' => 1],
            ['type' => 'system_category', 'value' => 'tubes_pipes', 'label' => 'Tubes & Pipes', 'sort_order' => 2],
            ['type' => 'system_category', 'value' => 'valves_fittings', 'label' => 'Valves & Fittings', 'sort_order' => 3],
            ['type' => 'system_category', 'value' => 'consumables', 'label' => 'Consumables', 'sort_order' => 4],
            ['type' => 'system_category', 'value' => 'chemicals', 'label' => 'Chemicals', 'sort_order' => 5],
            ['type' => 'system_category', 'value' => 'gases', 'label' => 'Gases', 'sort_order' => 6],
            ['type' => 'system_category', 'value' => 'custom_fabrication', 'label' => 'Custom Fabrication', 'sort_order' => 7],
            ['type' => 'system_category', 'value' => 'finished_product', 'label' => 'Finished Product', 'sort_order' => 8],
        ];

        foreach ($options as $option) {
            DropdownOption::firstOrCreate(
                ['type' => $option['type'], 'value' => $option['value']],
                $option
            );
        }
    }
}
