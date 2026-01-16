<?php
// database\migrations\0001_01_01_000000_create_users_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * * Created for Employee Management System.
     * Includes core auth, password recovery, and session persistence.
     */
    public function up(): void
    {
        // 1. USERS TABLE
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            // Authentication & Identity
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();

            // Employee Specific Metadata
            // employee_id is kept as string to support alphanumeric IDs (e.g., EMP-001)
            $table->string('employee_id')->unique();
            $table->string('department')->nullable()->index();
            $table->string('position')->nullable()->index();

            // Authorization
            // Refactored from ENUM to string for easier database-agnostic scaling.
            // Validation should be handled via App\Enums\UserRole.
            $table->string('role')->default('employee')->index();

            // Assets & State
            $table->string('profile_photo_path', 2048)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // 2. PASSWORD RESET TOKENS
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // 3. SESSIONS
        // Optimized for database-backed session drivers
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
