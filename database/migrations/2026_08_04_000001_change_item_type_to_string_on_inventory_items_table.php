<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * This widened item_type from an ENUM to a plain string. It was written as
     * a raw "ALTER TABLE ... MODIFY", which is MySQL-only syntax and fails
     * outright on any other driver, so a fresh install on SQLite (local dev,
     * CI, a test database) could never get past this point. The schema builder
     * expresses the same change portably.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('inventory_items', 'item_type')) {
            return;
        }

        Schema::table('inventory_items', function (Blueprint $table) {
            $table->string('item_type', 100)->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * The original ENUM only ever existed on MySQL, so it is restored only
     * there; elsewhere the column stays a string, which is what it already was.
     */
    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() !== 'mysql') {
            return;
        }

        Schema::getConnection()->statement(
            "ALTER TABLE inventory_items MODIFY item_type ENUM('raw_material','consumable','part','finished_good') NOT NULL"
        );
    }
};
