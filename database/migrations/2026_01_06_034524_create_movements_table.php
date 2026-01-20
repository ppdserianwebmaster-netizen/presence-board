<?php
// database/migrations/2026_01_06_034524_create_movements_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('movements', function (Blueprint $table) {
            $table->id();

            // Ownership
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            // Timing
            $table->timestamp('started_at')->useCurrent()->index();
            $table->timestamp('ended_at')->nullable()->index();

            // Classification & Context
            // Indexed for quick filtering (e.g., "Show all Lunch breaks")
            $table->string('type')->index(); 
            $table->string('remark', 500)->nullable();

            // Audit
            $table->timestamps();
            $table->softDeletes();

            /*
            |------------------------------------------------------------------
            | Composite Indexes (Optimized for Employee Presence Logic)
            |------------------------------------------------------------------
            */
            // "Where is employee X right now?" 
            $table->index(['user_id', 'started_at', 'ended_at'], 'idx_user_presence');
            
            // "Who is out between these dates?"
            $table->index(['started_at', 'ended_at'], 'idx_movement_range');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movements');
    }
};
