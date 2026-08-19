<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Converts status from an enum/CHECK-constrained column to a plain
     * string, same as item_type on inventory_items, so new workflow stages
     * don't need an enum-altering migration every time.
     *
     * Written originally as a raw "ALTER TABLE ... MODIFY" - MySQL-only
     * syntax that fails on every other driver, so a fresh install on SQLite
     * (local dev, CI, a test database) stopped here. The schema builder
     * expresses the same change portably.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('projects', 'status')) {
            return;
        }

        Schema::table('projects', function (Blueprint $table) {
            $table->string('status', 50)->default('draft')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * The ENUM only ever existed on MySQL, so it is restored only there;
     * elsewhere the column stays a string, which is what it already was.
     */
    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() !== 'mysql') {
            return;
        }

        Schema::getConnection()->statement(
            "ALTER TABLE projects MODIFY status ENUM('draft','confirmed','in_production','delayed','finished','delivered') NOT NULL DEFAULT 'draft'"
        );
    }
};
