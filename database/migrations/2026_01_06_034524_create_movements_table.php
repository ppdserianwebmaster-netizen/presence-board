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
        Schema::create('movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->index();
            $table->dateTime('start_datetime')->index();
            $table->dateTime('end_datetime')->nullable()->index();
            $table->enum('type', ['meeting', 'course', 'training', 'travel', 'leave', 'other'])->index();
            $table->text('note')->nullable();
            $table->enum('status', ['planned', 'active', 'completed', 'cancelled'])->default('planned')->index();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['user_id', 'start_datetime', 'end_datetime']);
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
