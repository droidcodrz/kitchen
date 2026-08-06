<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Converts status from an enum/CHECK-constrained column to a plain
     * string, same as item_type on inventory_items, so new workflow stages
     * don't need an enum-altering migration every time.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE projects MODIFY status VARCHAR(50) NOT NULL DEFAULT 'draft'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE projects MODIFY status ENUM('draft','confirmed','in_production','delayed','finished','delivered') NOT NULL DEFAULT 'draft'");
    }
};
