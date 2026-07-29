<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\CustomFieldDefinition;
use Illuminate\Database\Seeder;

class CustomFieldDefinitionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $metals = Category::where('slug', 'metals')->first();
        $tubesPipes = Category::where('slug', 'tubes-pipes')->first();
        $valvesFittings = Category::where('slug', 'valves-fittings')->first();
        $consumables = Category::where('slug', 'consumables')->first();
        $chemicals = Category::where('slug', 'chemicals')->first();
        $gases = Category::where('slug', 'gases')->first();
        $customFab = Category::where('slug', 'custom-fabrication')->first();

        $fields = [];

        if ($tubesPipes) {
            $fields[] = [
                'entity_type' => 'product',
                'category_id' => $tubesPipes->id,
                'applies_to_item_types' => json_encode(['Steel Tube', 'Angle Bar']),
                'field_name' => 'tube_type',
                'field_label' => 'Tube Type',
                'field_type' => 'select',
                'options' => json_encode(['Stainless Steel', 'Black Iron', 'Galvanized']),
                'is_required' => false,
                'sort_order' => 1,
            ];
            $fields[] = [
                'entity_type' => 'product',
                'category_id' => $tubesPipes->id,
                'applies_to_item_types' => json_encode(['Steel Tube']),
                'field_name' => 'diameter',
                'field_label' => 'Diameter (inches)',
                'field_type' => 'text',
                'options' => null,
                'is_required' => false,
                'sort_order' => 2,
            ];
            $fields[] = [
                'entity_type' => 'product',
                'category_id' => $tubesPipes->id,
                'applies_to_item_types' => json_encode(['Steel Tube', 'Angle Bar']),
                'field_name' => 'length',
                'field_label' => 'Length (feet)',
                'field_type' => 'number',
                'options' => null,
                'is_required' => false,
                'sort_order' => 3,
            ];
        }

        if ($valvesFittings) {
            $fields[] = [
                'entity_type' => 'product',
                'category_id' => $valvesFittings->id,
                'applies_to_item_types' => json_encode(['Gas Valve', 'Water Valve', 'Pipe Fitter']),
                'field_name' => 'size',
                'field_label' => 'Size (inches)',
                'field_type' => 'text',
                'options' => null,
                'is_required' => false,
                'sort_order' => 1,
            ];
            $fields[] = [
                'entity_type' => 'product',
                'category_id' => $valvesFittings->id,
                'applies_to_item_types' => json_encode(['Burner']),
                'field_name' => 'btu_rating',
                'field_label' => 'BTU Rating',
                'field_type' => 'text',
                'options' => null,
                'is_required' => false,
                'sort_order' => 2,
            ];
            $fields[] = [
                'entity_type' => 'product',
                'category_id' => $valvesFittings->id,
                'applies_to_item_types' => null,
                'field_name' => 'connection_type',
                'field_label' => 'Connection Type',
                'field_type' => 'text',
                'options' => null,
                'is_required' => false,
                'sort_order' => 3,
            ];
        }

        if ($consumables) {
            $fields[] = [
                'entity_type' => 'product',
                'category_id' => $consumables->id,
                'applies_to_item_types' => json_encode(['Nozzle', 'Torch Tip']),
                'field_name' => 'tip_size',
                'field_label' => 'Tip Size',
                'field_type' => 'text',
                'options' => null,
                'is_required' => false,
                'sort_order' => 1,
            ];
            $fields[] = [
                'entity_type' => 'product',
                'category_id' => $consumables->id,
                'applies_to_item_types' => json_encode(['Wire', 'Electrode', 'Welding Stick']),
                'field_name' => 'wire_diameter',
                'field_label' => 'Wire/Electrode Diameter (mm)',
                'field_type' => 'text',
                'options' => null,
                'is_required' => false,
                'sort_order' => 2,
            ];
            $fields[] = [
                'entity_type' => 'product',
                'category_id' => $consumables->id,
                'applies_to_item_types' => json_encode(['Wire', 'Electrode', 'Welding Stick']),
                'field_name' => 'material_specification',
                'field_label' => 'Material Specification',
                'field_type' => 'text',
                'options' => null,
                'is_required' => false,
                'sort_order' => 3,
            ];
            $fields[] = [
                'entity_type' => 'product',
                'category_id' => $consumables->id,
                'applies_to_item_types' => null,
                'field_name' => 'pack_quantity',
                'field_label' => 'Pack Quantity',
                'field_type' => 'number',
                'options' => null,
                'is_required' => false,
                'sort_order' => 4,
            ];
        }

        if ($chemicals) {
            $fields[] = [
                'entity_type' => 'product',
                'category_id' => $chemicals->id,
                'applies_to_item_types' => null,
                'field_name' => 'color',
                'field_label' => 'Color',
                'field_type' => 'text',
                'options' => null,
                'is_required' => false,
                'sort_order' => 1,
            ];
            $fields[] = [
                'entity_type' => 'product',
                'category_id' => $chemicals->id,
                'applies_to_item_types' => null,
                'field_name' => 'volume',
                'field_label' => 'Volume (oz/gal/ml)',
                'field_type' => 'text',
                'options' => null,
                'is_required' => false,
                'sort_order' => 2,
            ];
            $fields[] = [
                'entity_type' => 'product',
                'category_id' => $chemicals->id,
                'applies_to_item_types' => null,
                'field_name' => 'viscosity',
                'field_label' => 'Viscosity',
                'field_type' => 'text',
                'options' => null,
                'is_required' => false,
                'sort_order' => 3,
            ];
            $fields[] = [
                'entity_type' => 'product',
                'category_id' => $chemicals->id,
                'applies_to_item_types' => null,
                'field_name' => 'brand',
                'field_label' => 'Brand',
                'field_type' => 'text',
                'options' => null,
                'is_required' => false,
                'sort_order' => 4,
            ];
        }

        if ($gases) {
            $fields[] = [
                'entity_type' => 'product',
                'category_id' => $gases->id,
                'applies_to_item_types' => null,
                'field_name' => 'cylinder_size',
                'field_label' => 'Cylinder Size (cu ft)',
                'field_type' => 'text',
                'options' => null,
                'is_required' => false,
                'sort_order' => 1,
            ];
            $fields[] = [
                'entity_type' => 'product',
                'category_id' => $gases->id,
                'applies_to_item_types' => null,
                'field_name' => 'gas_purity',
                'field_label' => 'Gas Purity %',
                'field_type' => 'text',
                'options' => null,
                'is_required' => false,
                'sort_order' => 2,
            ];
            $fields[] = [
                'entity_type' => 'product',
                'category_id' => $gases->id,
                'applies_to_item_types' => null,
                'field_name' => 'pressure_rating',
                'field_label' => 'Pressure Rating (PSI)',
                'field_type' => 'text',
                'options' => null,
                'is_required' => false,
                'sort_order' => 3,
            ];
        }

        if ($customFab) {
            $fields[] = [
                'entity_type' => 'product',
                'category_id' => $customFab->id,
                'applies_to_item_types' => json_encode(['CNC Laser Cut']),
                'field_name' => 'design_file',
                'field_label' => 'Design File Reference',
                'field_type' => 'text',
                'options' => null,
                'is_required' => false,
                'sort_order' => 1,
            ];
            $fields[] = [
                'entity_type' => 'product',
                'category_id' => $customFab->id,
                'applies_to_item_types' => json_encode(['CNC Laser Cut']),
                'field_name' => 'cutting_complexity',
                'field_label' => 'Cutting Complexity',
                'field_type' => 'select',
                'options' => json_encode(['Simple', 'Moderate', 'Complex', 'Very Complex']),
                'is_required' => false,
                'sort_order' => 2,
            ];
            $fields[] = [
                'entity_type' => 'product',
                'category_id' => $customFab->id,
                'applies_to_item_types' => null,
                'field_name' => 'finish',
                'field_label' => 'Finish',
                'field_type' => 'text',
                'options' => null,
                'is_required' => false,
                'sort_order' => 3,
            ];
        }

        foreach ($fields as $field) {
            CustomFieldDefinition::updateOrCreate(
                [
                    'entity_type' => $field['entity_type'],
                    'category_id' => $field['category_id'],
                    'field_name' => $field['field_name'],
                ],
                $field
            );
        }

        foreach ($fields as &$field) {
            $field['entity_type'] = 'inventory_item';
        }

        foreach ($fields as $field) {
            CustomFieldDefinition::updateOrCreate(
                [
                    'entity_type' => $field['entity_type'],
                    'category_id' => $field['category_id'],
                    'field_name' => $field['field_name'],
                ],
                $field
            );
        }
    }
}
