<?php
// database/migrations/2026_01_09_000000_create_users_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Users Table Migration (Laravel 12)
 *
 * This migration defines the core authentication table (users)
 * together with supporting tables required by Laravel's
 * authentication, password reset, and database session features.
 *
 * Design goals:
 * - Optimized for employee-based systems
 * - Indexes aligned with common query patterns
 * - Safe soft-deletion for audit/history use cases
 * - Fully compatible with Laravel 11/12 defaults
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Tables created:
     * - users: Main employee and administrator records
     * - password_reset_tokens: Password reset support (Laravel default)
     * - sessions: Database-backed session storage
     */
    public function up(): void
    {
        /**
         * USERS TABLE
         *
         * Stores authentication credentials and employee metadata.
         */
        Schema::create('users', function (Blueprint $table) {
            // Primary key
            $table->id();

            /*
             |------------------------------------------------------------------
             | Authentication Fields
             |------------------------------------------------------------------
             */
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');

            /*
             |------------------------------------------------------------------
             | Employee Metadata
             |------------------------------------------------------------------
             */
            $table->string('employee_id')->unique();
            $table->string('department')->nullable()->index();
            $table->string('position')->nullable()->index();

            /*
             |------------------------------------------------------------------
             | Authorization / Role Management
             |------------------------------------------------------------------
             | Enum is acceptable for small, fixed role sets.
             | If roles expand in future, migrate to a roles table.
             */
            $table->enum('role', ['admin', 'employee'])
                ->default('employee')
                ->index();

            /*
             |------------------------------------------------------------------
             | Profile & Session Helpers
             |------------------------------------------------------------------
             */
            $table->string('profile_photo_path')->nullable();
            $table->rememberToken();

            /*
             |------------------------------------------------------------------
             | Timestamps & Soft Deletes
             |------------------------------------------------------------------
             | Soft deletes allow logical removal while preserving audit trails.
             */
            $table->timestamps();
            $table->softDeletes();
        });

        /**
         * PASSWORD RESET TOKENS TABLE
         *
         * Used by Laravel's built-in password reset feature.
         */
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        /**
         * SESSIONS TABLE
         *
         * Required when using the database session driver.
         * Supports user tracking and session invalidation.
         */
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->cascadeOnDelete();

            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * Drops tables in dependency-safe order.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
