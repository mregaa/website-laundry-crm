<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Step 1: Temporarily allow 'in_progress' in the enum
     * Step 2: Update existing data to use new status values
     */
    public function up(): void
    {
        // Step 1: First, we need to alter the enum to include 'in_progress'
        // This allows us to set the new value in Step 2
        DB::statement("ALTER TABLE orders MODIFY status ENUM('received', 'sorting', 'washing', 'drying', 'ironing', 'folding', 'ready', 'out_for_delivery', 'completed', 'cancelled', 'in_progress') DEFAULT 'received'");
        
        // Step 2: Now we can safely update old statuses to new ones
        DB::table('orders')
            ->whereIn('status', ['received', 'sorting', 'washing', 'drying', 'ironing', 'folding', 'out_for_delivery'])
            ->update(['status' => 'in_progress']);
        
        // ready, completed, cancelled stay the same - no changes needed
    }

    /**
     * Reverse the migrations.
     * 
     * Note: This is a lossy migration. We can only revert to 'received' 
     * as we don't know which specific processing stage orders were in.
     */
    public function down(): void
    {
        // Revert in_progress back to received as a default
        DB::table('orders')
            ->where('status', 'in_progress')
            ->update(['status' => 'received']);
        
        // Remove in_progress from the enum
        DB::statement("ALTER TABLE orders MODIFY status ENUM('received', 'sorting', 'washing', 'drying', 'ironing', 'folding', 'ready', 'out_for_delivery', 'completed', 'cancelled') DEFAULT 'received'");
    }
};
