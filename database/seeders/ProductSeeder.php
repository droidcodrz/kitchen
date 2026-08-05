<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\InventoryItem;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $commercialCategory = Category::where('slug', 'commercial-kitchen-equipment')->first();
        $refrigerationCategory = Category::where('slug', 'refrigeration')->first();
        $cookingCategory = Category::where('slug', 'cooking-equipment')->first();
        $prepCategory = Category::where('slug', 'food-preparation')->first();

        $products = [
            [
                'name' => 'Commercial Refrigerator - 4 Door',
                'slug' => 'commercial-refrigerator-4-door',
                'sku' => 'PROD-REF-4D-001',
                'category_id' => $refrigerationCategory->id,
                'type' => 'Equipment',
                'unit_price' => 85000.00,
                'description' => 'Heavy-duty 4-door commercial refrigerator with digital temperature control',
                'is_active' => true,
            ],
            [
                'name' => 'Industrial Gas Stove - 6 Burner',
                'slug' => 'industrial-gas-stove-6-burner',
                'sku' => 'PROD-STV-6B-001',
                'category_id' => $cookingCategory->id,
                'type' => 'Equipment',
                'unit_price' => 45000.00,
                'description' => '6-burner industrial gas stove with heavy-duty cast iron grates',
                'is_active' => true,
            ],
            [
                'name' => 'Commercial Deep Freezer',
                'slug' => 'commercial-deep-freezer',
                'sku' => 'PROD-FRZ-COM-001',
                'category_id' => $refrigerationCategory->id,
                'type' => 'Equipment',
                'unit_price' => 72000.00,
                'description' => 'Large capacity deep freezer for commercial use',
                'is_active' => true,
            ],
            [
                'name' => 'Industrial Mixer - 20L',
                'slug' => 'industrial-mixer-20l',
                'sku' => 'PROD-MIX-20L-001',
                'category_id' => $prepCategory->id,
                'type' => 'Equipment',
                'unit_price' => 38000.00,
                'description' => '20-liter capacity industrial food mixer with multiple speed settings',
                'is_active' => true,
            ],
            [
                'name' => 'Commercial Oven - Convection',
                'slug' => 'commercial-oven-convection',
                'sku' => 'PROD-OVN-CNV-001',
                'category_id' => $cookingCategory->id,
                'type' => 'Equipment',
                'unit_price' => 95000.00,
                'description' => 'Professional convection oven with digital controls',
                'is_active' => true,
            ],
            [
                'name' => 'Display Refrigerator - Glass Door',
                'slug' => 'display-refrigerator-glass-door',
                'sku' => 'PROD-REF-DSP-001',
                'category_id' => $refrigerationCategory->id,
                'type' => 'Equipment',
                'unit_price' => 68000.00,
                'description' => 'Glass door display refrigerator with LED lighting',
                'is_active' => true,
            ],
            [
                'name' => 'Commercial Food Processor',
                'slug' => 'commercial-food-processor',
                'sku' => 'PROD-FP-COM-001',
                'category_id' => $prepCategory->id,
                'type' => 'Equipment',
                'unit_price' => 32000.00,
                'description' => 'Heavy-duty food processor for commercial kitchens',
                'is_active' => true,
            ],
            [
                'name' => 'Industrial Griddle - Electric',
                'slug' => 'industrial-griddle-electric',
                'sku' => 'PROD-GRD-ELC-001',
                'category_id' => $cookingCategory->id,
                'type' => 'Equipment',
                'unit_price' => 52000.00,
                'description' => 'Large electric griddle with temperature zones',
                'is_active' => true,
            ],
            [
                'name' => 'Walk-in Cooler Panel Kit',
                'slug' => 'walk-in-cooler-panel-kit',
                'sku' => 'PROD-WIC-PNL-001',
                'category_id' => $refrigerationCategory->id,
                'type' => 'Equipment',
                'unit_price' => 125000.00,
                'description' => 'Complete panel kit for walk-in cooler construction',
                'is_active' => true,
            ],
            [
                'name' => 'Halal Kitchen Setup - Marinari',
                'slug' => 'halal-kitchen-setup-marinari',
                'sku' => 'PROD-HKS-MAR-001',
                'category_id' => $commercialCategory->id,
                'type' => 'Package',
                'unit_price' => 450000.00,
                'description' => 'Complete Halal-certified kitchen setup for Marinari standards',
                'is_active' => true,
            ],
            [
                'name' => 'Cloud Kitchen Equipment Package',
                'slug' => 'cloud-kitchen-equipment-package',
                'sku' => 'PROD-CKE-PKG-001',
                'category_id' => $commercialCategory->id,
                'type' => 'Package',
                'unit_price' => 380000.00,
                'description' => 'Complete equipment package for cloud kitchen setup',
                'is_active' => true,
            ],
        ];

        foreach ($products as $productData) {
            $product = Product::firstOrCreate(['sku' => $productData['sku']], $productData);

            if (! $product->wasRecentlyCreated) {
                continue;
            }

            // Attach random inventory items as required materials (2-4 items per product)
            $materialIds = InventoryItem::inRandomOrder()->limit(rand(2, 4))->pluck('id');
            if ($materialIds->count() > 0) {
                $product->requiredMaterials()->attach($materialIds);
            }
        }
    }
}
