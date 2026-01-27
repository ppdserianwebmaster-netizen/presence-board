<?php

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
            // Refactor: precision 6 is great. We'll ensure these are indexed for range queries.
            $table->timestamp('started_at', 6)->useCurrent()->index();
            $table->timestamp('ended_at', 6)->nullable()->index();

            // Classification
            // PHP 8.4 Tip: This will map to a Backed Enum (e.g., MovementType::CheckIn)
            $table->string('type')->index()->comment('Values: meeting, course, travel, leave, other'); 
            $table->string('remark', 500)->nullable();

            // Logic Enhancement: Virtual Column (MySQL 8.0+ / MariaDB 10.2+)
            // This calculates the duration in seconds automatically at the DB level.
            // Very useful for Livewire reports using Excel (maatwebsite/excel).
            $table->unsignedInteger('duration_seconds')
                ->virtualAs('TIMESTAMPDIFF(SECOND, started_at, ended_at)')
                ->nullable();

            // Audit & State
            $table->timestamps();
            $table->softDeletes();

            /*
            |------------------------------------------------------------------
            | Composite Indexes
            |------------------------------------------------------------------
            */
            
            // Performance: For User::with('latestMovement')
            $table->index(['user_id', 'started_at', 'type'], 'idx_user_movement_history');
            
            // Real-time: "Who is currently on a break/duty?"
            // Filtering where ended_at IS NULL is much faster with this index.
            $table->index(['ended_at', 'user_id'], 'idx_currently_active_users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movements');
    }
};
