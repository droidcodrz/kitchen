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
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->string('name', 191);
            $table->string('sku', 100)->unique();
            $table->enum('item_type', ['raw_material', 'consumable', 'part', 'finished_good']);
            $table->decimal('stock_quantity', 12, 2)->default(0);
            $table->decimal('reserved_quantity', 12, 2)->default(0);
            $table->decimal('minimum_stock_level', 12, 2)->default(0);
            $table->decimal('unit_price', 12, 2)->default(0);
            $table->string('unit_of_measure', 50)->default('pcs');
            $table->foreignId('vendor_id')->nullable()->constrained('vendors')->nullOnDelete();
            $table->foreignId('storage_location_id')->nullable()->constrained('storage_locations')->nullOnDelete();
            $table->decimal('last_added_quantity', 12, 2)->nullable();
            $table->timestamp('last_added_at')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('sku');
            $table->index('item_type');
            $table->index('vendor_id');
            $table->index('is_active');
            $table->index(['stock_quantity', 'minimum_stock_level']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_items');
    }
};
