<?php

namespace Database\Seeders;

use App\Models\InventoryItem;
use App\Models\StorageLocation;
use App\Models\Vendor;
use Illuminate\Database\Seeder;

class InventoryItemSeeder extends Seeder
{
    public function run(): void
    {
        $vendor = Vendor::first();
        $storageLocation = StorageLocation::where('code', 'WH-MAIN')->first();

        $items = [
            [
                'name' => 'Stainless Steel Sheet 304',
                'sku' => 'SS-304-001',
                'item_type' => 'raw_material',
                'description' => 'High-grade stainless steel sheet for kitchen equipment',
                'unit_of_measure' => 'sheet',
                'stock_quantity' => 150.00,
                'reserved_quantity' => 0.00,
                'minimum_stock_level' => 50.00,
                'unit_price' => 2500.00,
                'vendor_id' => $vendor->id,
                'storage_location_id' => $storageLocation->id,
                'is_active' => true,
            ],
            [
                'name' => 'Commercial Burner Assembly',
                'sku' => 'BUR-COM-001',
                'item_type' => 'part',
                'description' => 'Heavy-duty burner for commercial stoves',
                'unit_of_measure' => 'piece',
                'stock_quantity' => 45.00,
                'reserved_quantity' => 0.00,
                'minimum_stock_level' => 20.00,
                'unit_price' => 1200.00,
                'vendor_id' => $vendor->id,
                'storage_location_id' => $storageLocation->id,
                'is_active' => true,
            ],
            [
                'name' => 'Compressor Unit - 2HP',
                'sku' => 'COMP-2HP-001',
                'item_type' => 'part',
                'description' => '2HP compressor for refrigeration units',
                'unit_of_measure' => 'piece',
                'stock_quantity' => 8.00,
                'reserved_quantity' => 0.00,
                'minimum_stock_level' => 10.00,
                'unit_price' => 8500.00,
                'vendor_id' => $vendor->id,
                'storage_location_id' => $storageLocation->id,
                'is_active' => true,
            ],
            [
                'name' => 'Control Panel PCB',
                'sku' => 'PCB-CTRL-001',
                'item_type' => 'part',
                'description' => 'Digital control panel circuit board',
                'unit_of_measure' => 'piece',
                'stock_quantity' => 32.00,
                'reserved_quantity' => 0.00,
                'minimum_stock_level' => 25.00,
                'unit_price' => 650.00,
                'vendor_id' => $vendor->id,
                'storage_location_id' => $storageLocation->id,
                'is_active' => true,
            ],
            [
                'name' => 'Door Hinge - Heavy Duty',
                'sku' => 'HNG-HD-001',
                'item_type' => 'part',
                'description' => 'Industrial door hinge for refrigerators',
                'unit_of_measure' => 'piece',
                'stock_quantity' => 5.00,
                'reserved_quantity' => 0.00,
                'minimum_stock_level' => 15.00,
                'unit_price' => 180.00,
                'vendor_id' => $vendor->id,
                'storage_location_id' => $storageLocation->id,
                'is_active' => true,
            ],
            [
                'name' => 'Insulation Foam Panel',
                'sku' => 'INS-FOAM-001',
                'item_type' => 'raw_material',
                'description' => 'Thermal insulation for cold storage',
                'unit_of_measure' => 'panel',
                'stock_quantity' => 85.00,
                'reserved_quantity' => 0.00,
                'minimum_stock_level' => 40.00,
                'unit_price' => 320.00,
                'vendor_id' => $vendor->id,
                'storage_location_id' => $storageLocation->id,
                'is_active' => true,
            ],
            [
                'name' => 'Temperature Sensor',
                'sku' => 'SENS-TEMP-001',
                'item_type' => 'part',
                'description' => 'Digital temperature sensor with display',
                'unit_of_measure' => 'piece',
                'stock_quantity' => 3.00,
                'reserved_quantity' => 0.00,
                'minimum_stock_level' => 12.00,
                'unit_price' => 450.00,
                'vendor_id' => $vendor->id,
                'storage_location_id' => $storageLocation->id,
                'is_active' => true,
            ],
            [
                'name' => 'Gas Valve Assembly',
                'sku' => 'GAS-VLV-001',
                'item_type' => 'part',
                'description' => 'Safety gas valve for cooking equipment',
                'unit_of_measure' => 'piece',
                'stock_quantity' => 18.00,
                'reserved_quantity' => 0.00,
                'minimum_stock_level' => 15.00,
                'unit_price' => 890.00,
                'vendor_id' => $vendor->id,
                'storage_location_id' => $storageLocation->id,
                'is_active' => true,
            ],
            [
                'name' => 'Aluminum Sheet 3mm',
                'sku' => 'AL-SHT-3MM',
                'item_type' => 'raw_material',
                'description' => '3mm aluminum sheet for paneling',
                'unit_of_measure' => 'sheet',
                'stock_quantity' => 125.00,
                'reserved_quantity' => 0.00,
                'minimum_stock_level' => 60.00,
                'unit_price' => 780.00,
                'vendor_id' => $vendor->id,
                'storage_location_id' => $storageLocation->id,
                'is_active' => true,
            ],
            [
                'name' => 'Motor Assembly 1.5HP',
                'sku' => 'MTR-1.5HP-001',
                'item_type' => 'part',
                'description' => 'Electric motor for mixers and processors',
                'unit_of_measure' => 'piece',
                'stock_quantity' => 22.00,
                'reserved_quantity' => 0.00,
                'minimum_stock_level' => 18.00,
                'unit_price' => 3200.00,
                'vendor_id' => $vendor->id,
                'storage_location_id' => $storageLocation->id,
                'is_active' => true,
            ],
        ];

        foreach ($items as $item) {
            InventoryItem::create($item);
        }
    }
}
