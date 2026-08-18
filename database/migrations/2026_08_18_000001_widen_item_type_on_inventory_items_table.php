<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * inventory_items.item_type was created as an ENUM of four values, and the
     * migration meant to widen it used "ALTER TABLE ... MODIFY", which is MySQL
     * syntax - on any other driver it does not apply, so the original
     * constraint survives. Meanwhile the Item Type dropdown offers a dozen
     * values ("Sheet Metal", "Burner", ...), none of which the constraint
     * allows, so saving a material fails at the database with a 500.
     *
     * Restate the change through the schema builder so it applies on every
     * driver, including databases where the earlier migration is already
     * recorded as run.
     */
    public function up(): void
    {
        Schema::table('inventory_items', function (Blueprint $table) {
            $table->string('item_type', 100)->nullable(false)->change();
        });
    }

    public function down(): void
    {
        // Deliberately not restoring the old four-value constraint: existing
        // rows legitimately hold values outside it, and narrowing the column
        // back would fail or silently drop them.
    }
};
