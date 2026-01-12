<?php
// database\migrations\2026_01_09_000001_create_movements_table.php
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
        Schema::create('movements', function (Blueprint $table) {
            $table->id();
            
            // Foreign key to user
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Movement timestamps
            $table->dateTime('started_at');
            $table->dateTime('ended_at')->nullable();
            
            // Movement type & status
            $table->enum('movement_type', ['meeting', 'course', 'training', 'travel', 'leave', 'other']);
            $table->enum('status', ['planned', 'active', 'completed', 'cancelled'])->default('planned');
            
            // Notes / remarks
            $table->text('remark')->nullable();
            
            // Laravel standard
            $table->timestamps();
            $table->softDeletes();
            
            // ============================================
            // OPTIMIZED INDEXES FOR PRESENCE BOARD
            // ============================================
            
            // Composite index for the most common query pattern in presence board
            // This covers: WHERE user_id = ? AND status IN ('planned', 'active') AND started_at <= NOW()
            $table->index(['user_id', 'status', 'started_at'], 'idx_user_status_start');
            
            // Index for date range queries (when checking ended_at >= NOW())
            $table->index(['started_at', 'ended_at'], 'idx_date_range');
            
            // Individual indexes for filtering
            $table->index('status', 'idx_status');
            $table->index('movement_type', 'idx_movement_type');
            
            // Composite index for active movements check
            // Covers: WHERE status IN ('planned', 'active') AND started_at <= NOW() AND ended_at >= NOW()
            $table->index(['status', 'started_at', 'ended_at'], 'idx_active_movements');
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
