<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Guarded so the migration is safe to re-run against a database that
        // already carries this column - re-running otherwise aborts with a
        // duplicate-column error and blocks every later migration.
        if (Schema::hasColumn('inventory_items', 'diameter')) {
            return;
        }

        Schema::table('inventory_items', function (Blueprint $table) {
            $table->string('diameter', 50)->nullable()->after('dimension');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory_items', function (Blueprint $table) {
            $table->dropColumn('diameter');
        });
    }
};
