<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // Core setup
            RolesSeeder::class,
            PermissionsSeeder::class,
            PermissionRoleSeeder::class,
            AlertConfigurationSeeder::class,
            // Fills the dropdowns on the product and inventory forms - item
            // type, material type, grade, gauge, inventory type. Not sample
            // data: without it those selects render with no options at all.
            DropdownOptionSeeder::class,
            AdminUserSeeder::class,

            // Sample data
            CategorySeeder::class,
            VendorSeeder::class,
            StorageLocationSeeder::class,
            ClientSeeder::class,
            InventoryItemSeeder::class,
            ProductSeeder::class,
            TeamSeeder::class,
            ProjectSeeder::class,
        ]);
    }
}
