<?php
// database\migrations\2026_01_06_034524_create_movements_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * * Tracks employee movements/absences. Status is derived via:
     * - Active: started_at <= now AND (ended_at NULL OR ended_at > now)
     */
    public function up(): void
    {
        Schema::create('movements', function (Blueprint $table) {
            $table->id();

            // Foreign Key with optimized indexing for deletion performance
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            // Timing (Core Logic)
            // Use timestamp() or dateTime() - timestamp is often preferred for timezone consistency
            $table->timestamp('started_at')->useCurrent();
            $table->timestamp('ended_at')->nullable();

            // Type classification
            // Refactored to string for better compatibility with PHP 8.2 Enums
            $table->string('type')->index(); 
            
            // Context
            $table->string('remark', 500)->nullable();

            // Audit
            $table->timestamps();
            $table->softDeletes();

            /*
            |------------------------------------------------------------------
            | Composite Indexes
            |------------------------------------------------------------------
            */
            
            // Optimized for: "Where is employee X right now?"
            $table->index(['user_id', 'started_at', 'ended_at'], 'idx_user_current_location');

            // Optimized for: "Who is out of the office today?" (Range scan)
            $table->index(['started_at', 'ended_at'], 'idx_global_timeline');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movements');
    }
};
