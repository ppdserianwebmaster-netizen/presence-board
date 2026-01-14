<?php
// database/migrations/2026_01_09_000001_create_movements_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Movements Table Migration (Laravel 12)
 *
 * Tracks employee movements / absences using a time-based model.
 * No explicit status column is stored; movement state is derived
 * dynamically from datetime comparisons.
 *
 * Active Movement Definition:
 * - started_at <= NOW()
 * - AND (ended_at IS NULL OR ended_at > NOW())
 *
 * This design avoids status desynchronization issues and keeps
 * historical data immutable.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('movements', function (Blueprint $table) {
            // Primary key
            $table->id();

            /*
             |------------------------------------------------------------------
             | Relationships
             |------------------------------------------------------------------
             */
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            /*
             |------------------------------------------------------------------
             | Movement Time Window (Core Business Logic)
             |------------------------------------------------------------------
             | Movement state (active / inactive / upcoming) is calculated
             | entirely from these two columns.
             */
            $table->dateTime('started_at');
            $table->dateTime('ended_at')->nullable();

            /*
             |------------------------------------------------------------------
             | Classification
             |------------------------------------------------------------------
             | Enum is suitable here due to a controlled, finite set of values.
             | If extensibility is required, migrate to a lookup table.
             */
            $table->enum('movement_type', [
                'meeting',
                'course',
                'training',
                'travel',
                'leave',
                'other',
            ]);

            /*
             |------------------------------------------------------------------
             | Additional Context
             |------------------------------------------------------------------
             */
            $table->text('remark')->nullable();

            /*
             |------------------------------------------------------------------
             | Laravel Standard Columns
             |------------------------------------------------------------------
             */
            $table->timestamps();
            $table->softDeletes();

            /*
             |==================================================================
             | Performance Indexes
             |==================================================================
             | Index strategy is optimized for presence board queries
             | and historical reporting.
             */

            // Active movement lookup per user
            // WHERE user_id = ?
            //   AND started_at <= NOW()
            //   AND (ended_at IS NULL OR ended_at > NOW())
            $table->index(['user_id', 'started_at', 'ended_at'], 'idx_user_active_movement');

            // Global active movement range scans
            // WHERE started_at <= NOW() AND ended_at > NOW()
            $table->index(['started_at', 'ended_at'], 'idx_date_range');

            // Filtering by movement type (reports, analytics)
            $table->index('movement_type', 'idx_movement_type');

            // User movement history ordering
            // WHERE user_id = ? ORDER BY started_at DESC
            $table->index(['user_id', 'started_at'], 'idx_user_started');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movements');
    }
};
