<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Each column is guarded individually: a database that already has
        // some of them (added by hand or by an earlier partial run) still
        // picks up the rest instead of aborting on the first duplicate.
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'item_label')) {
                $table->string('item_label', 191)->nullable()->after('name');
            }

            if (!Schema::hasColumn('products', 'inventory_type')) {
                $table->string('inventory_type', 100)->nullable()->after('type');
            }

            if (!Schema::hasColumn('products', 'item_type')) {
                $table->string('item_type', 100)->nullable()->after('inventory_type');
            }

            if (!Schema::hasColumn('products', 'material_type')) {
                $table->string('material_type', 100)->nullable()->after('item_type');
            }

            if (!Schema::hasColumn('products', 'material_grade')) {
                $table->string('material_grade', 50)->nullable()->after('material_type');
            }

            if (!Schema::hasColumn('products', 'thickness_gauge')) {
                $table->string('thickness_gauge', 50)->nullable()->after('material_grade');
            }

            if (!Schema::hasColumn('products', 'thickness_mm')) {
                $table->decimal('thickness_mm', 8, 4)->nullable()->after('thickness_gauge');
            }

            if (!Schema::hasColumn('products', 'dimension')) {
                $table->string('dimension', 50)->nullable()->after('thickness_mm');
            }

            if (!Schema::hasColumn('products', 'unit_sale_price')) {
                $table->decimal('unit_sale_price', 12, 2)->nullable()->after('unit_price');
            }

            if (!Schema::hasColumn('products', 'item_label')) {
                $table->index('item_label');
            }

            if (!Schema::hasColumn('products', 'material_type')) {
                $table->index('material_type');
            }

            if (!Schema::hasColumn('products', 'item_type')) {
                $table->index('item_type');
            }

        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['item_label']);
            $table->dropIndex(['material_type']);
            $table->dropIndex(['item_type']);
            $table->dropColumn([
                'item_label',
                'inventory_type',
                'item_type',
                'material_type',
                'material_grade',
                'thickness_gauge',
                'thickness_mm',
                'dimension',
                'unit_sale_price',
            ]);
        });
    }
};
