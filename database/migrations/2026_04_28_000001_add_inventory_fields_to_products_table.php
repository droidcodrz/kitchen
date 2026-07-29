<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('item_label', 191)->nullable()->after('name');
            $table->string('inventory_type', 100)->nullable()->after('type');
            $table->string('item_type', 100)->nullable()->after('inventory_type');
            $table->string('material_type', 100)->nullable()->after('item_type');
            $table->string('material_grade', 50)->nullable()->after('material_type');
            $table->string('thickness_gauge', 50)->nullable()->after('material_grade');
            $table->decimal('thickness_mm', 8, 4)->nullable()->after('thickness_gauge');
            $table->string('dimension', 50)->nullable()->after('thickness_mm');
            $table->decimal('unit_sale_price', 12, 2)->nullable()->after('unit_price');

            $table->index('item_label');
            $table->index('material_type');
            $table->index('item_type');
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
