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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->string('expense_number')->unique();
            $table->enum('category', [
                'salary',
                'utilities',
                'supplies',
                'maintenance',
                'marketing',
                'rent',
                'equipment',
                'transportation',
                'other'
            ]);
            $table->decimal('amount', 10, 2);
            $table->string('vendor')->nullable();
            $table->text('description');
            $table->date('expense_date');
            $table->string('receipt_path')->nullable();
            $table->timestamps();
            
            $table->index('expense_number');
            $table->index('category');
            $table->index('expense_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
