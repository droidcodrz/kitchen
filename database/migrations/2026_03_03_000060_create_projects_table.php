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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('order_no', 50)->unique();
            $table->string('name', 191);
            $table->string('slug', 191)->unique();
            $table->foreignId('client_id')->constrained('clients')->restrictOnDelete();
            $table->foreignId('project_manager_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['draft', 'confirmed', 'in_production', 'delayed', 'finished', 'delivered'])->default('draft');
            $table->date('proposal_signed_date')->nullable();
            $table->date('delivery_date')->nullable();
            $table->date('production_deadline')->nullable();
            $table->date('actual_delivery_date')->nullable();
            $table->text('description')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('order_no');
            $table->index('status');
            $table->index('client_id');
            $table->index('project_manager_id');
            $table->index('delivery_date');
            $table->index('production_deadline');
            $table->index('slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
