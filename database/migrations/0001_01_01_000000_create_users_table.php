<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     * Note: Using PHP 8.4 property hooks in Models later will 
     * complement these column definitions perfectly.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            // Identity
            $table->id();
            
            // Auth & Personal Info
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();

            // Employee Metadata
            // Refactor: Added 'after' logic concept. In PHP 8.4, we can use 
            // asymmetric visibility (public readonly) for employee_id in the Model.
            $table->string('employee_id')->unique()->comment('Company specific ID e.g. EMP-2026-001');
            $table->string('department')->nullable()->index(); // Added index for individual filtering
            $table->string('position')->nullable();

            // Role Management
            // Refactor: Defaulting to a string that maps to a PHP 8.4 Backed Enum
            $table->string('role')->default('employee')->index();

            // Assets & State
            $table->string('profile_photo_path', 2048)->nullable();
            $table->boolean('is_active')->default(true)->index(); // Added to help Livewire filters
            
            $table->timestamps();
            $table->softDeletes(); 

            // Composite Performance Index
            // Optimal for: User::where('department', 'IT')->where('role', 'admin')->get();
            $table->index(['department', 'role', 'position'], 'idx_hr_search_criteria');
        });

        // Password Reset Tokens
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // Sessions
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            // Refactor: cascadeOnDelete ensures session cleanup when user is hard-deleted
            $table->foreignId('user_id')->nullable()->index()->constrained()->cascadeOnDelete();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
