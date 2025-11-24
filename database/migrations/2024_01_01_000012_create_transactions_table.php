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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_number')->unique();
            $table->enum('type', ['income', 'expense'])->default('income');
            $table->enum('category', [
                'order_payment',
                'salary',
                'utilities',
                'supplies',
                'maintenance',
                'marketing',
                'rent',
                'other'
            ]);
            $table->decimal('amount', 10, 2);
            $table->foreignId('order_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('payment_method', ['cash', 'card', 'bank_transfer', 'e-wallet'])->nullable();
            $table->text('description')->nullable();
            $table->date('transaction_date');
            $table->timestamps();
            
            $table->index('transaction_number');
            $table->index('type');
            $table->index('category');
            $table->index('transaction_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
