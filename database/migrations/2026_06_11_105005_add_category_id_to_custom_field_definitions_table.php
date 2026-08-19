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
        // already carries these columns.
        if (Schema::hasColumn('custom_field_definitions', 'category_id')) {
            return;
        }

        Schema::table('custom_field_definitions', function (Blueprint $table) {
            $table->unsignedBigInteger('category_id')->nullable()->after('entity_type');
            $table->string('applies_to_item_types', 500)->nullable()->after('category_id');

            $table->foreign('category_id')
                ->references('id')
                ->on('categories')
                ->nullOnDelete();

            $table->index('category_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('custom_field_definitions', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropIndex(['category_id']);
            $table->dropColumn(['category_id', 'applies_to_item_types']);
        });
    }
};
