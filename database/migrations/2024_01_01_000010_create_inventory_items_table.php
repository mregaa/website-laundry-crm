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
            $table->string('name');
            $table->string('sku')->unique();
            $table->text('description')->nullable();
            $table->enum('category', ['detergent', 'fabric_softener', 'bleach', 'starch', 'hangers', 'bags', 'other']);
            $table->decimal('quantity', 10, 2);
            $table->string('unit')->default('piece');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('reorder_level', 10, 2);
            $table->decimal('max_stock_level', 10, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('sku');
            $table->index('category');
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
