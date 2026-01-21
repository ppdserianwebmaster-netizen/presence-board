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

            // Timing (Using microsecond precision for high-frequency logs if needed)
            $table->timestamp('started_at', 6)->useCurrent()->index();
            $table->timestamp('ended_at', 6)->nullable()->index();

            // Classification (Refactored for PHP 8.4 Enums)
            $table->string('type')->index(); 
            $table->string('remark', 500)->nullable();

            // Audit & State
            $table->timestamps();
            $table->softDeletes();

            /*
            |------------------------------------------------------------------
            | Composite Indexes (Optimized for Laravel 12 Query Engine)
            |------------------------------------------------------------------
            */
            
            // Optimization: Helps the latestOfMany() relationship in your User model
            $table->index(['user_id', 'started_at'], 'idx_user_latest_movement');
            
            // "Where is everyone right now?" (Filtering by null ended_at)
            $table->index(['ended_at', 'type'], 'idx_active_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movements');
    }
};
