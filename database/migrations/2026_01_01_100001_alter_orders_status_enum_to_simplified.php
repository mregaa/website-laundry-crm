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
     * Step 3: Remove old enum values, keeping only simplified statuses.
     * This migration runs AFTER data has been migrated in the previous migration.
     */
    public function up(): void
    {
        // Now that all data is migrated, we can safely remove old enum values
        DB::statement("ALTER TABLE orders MODIFY status ENUM('in_progress', 'ready', 'completed', 'cancelled') DEFAULT 'in_progress'");
    }

    /**
     * Reverse the migrations.
     * 
     * Reverts to the original detailed status enum.
     */
    public function down(): void
    {
        // Add back all old enum values
        DB::statement("ALTER TABLE orders MODIFY status ENUM('received', 'sorting', 'washing', 'drying', 'ironing', 'folding', 'ready', 'out_for_delivery', 'completed', 'cancelled') DEFAULT 'received'");
    }
};
