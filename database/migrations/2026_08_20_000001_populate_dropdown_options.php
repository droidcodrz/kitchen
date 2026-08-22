<?php

use Database\Seeders\DropdownOptionSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Fills the dropdown_options table.
     *
     * The seeder for these options existed but was never registered, so the
     * table stayed empty on every install. Item Type, Material Type, Material
     * Grade, Thickness Gauge and Inventory Type all read from it, so those
     * selects rendered with no options at all - and a product's saved value
     * had no matching option to appear as selected, which looked like the
     * value was missing rather than merely unlistable.
     *
     * Registering the seeder fixes new installs; this fixes the databases
     * already out there, which are not re-seeded on deploy. The seeder matches
     * on type and value before inserting, so re-running it adds nothing twice
     * and leaves any option that was edited by hand exactly as it is.
     */
    public function up(): void
    {
        if (!Schema::hasTable('dropdown_options')) {
            return;
        }

        (new DropdownOptionSeeder())->run();
    }

    public function down(): void
    {
        // Reference data that saved products point at. Removing it would put
        // the forms back to showing empty dropdowns, so this is deliberately
        // not reversible.
    }
};
