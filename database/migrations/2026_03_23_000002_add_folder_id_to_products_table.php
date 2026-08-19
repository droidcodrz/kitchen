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
        // already carries this column.
        if (Schema::hasColumn('products', 'folder_id')) {
            return;
        }

        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('folder_id')->nullable()->after('category_id')->constrained('product_folders')->nullOnDelete();
            $table->index('folder_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['folder_id']);
            $table->dropIndex(['folder_id']);
            $table->dropColumn('folder_id');
        });
    }
};
