<?php

namespace Database\Factories;

use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * User Factory - Optimized for Laravel 12 & PHP 8.4
 * Handles high-speed data generation for testing and local development.
 */
class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = User::class;

    /**
     * The cached hash for the default password.
     */
    protected static ?string $password = null;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            
            /**
             * Performance Optimization: Cache the hash so XAMPP doesn't 
             * re-calculate Argon2ID/Bcrypt for every single user row.
             */
            'password' => static::$password ??= Hash::make('password'),
            
            'employee_id' => fake()->unique()->bothify('EMP-####-??'), 
            'department' => fake()->randomElement(['IT', 'HR', 'Finance', 'Admin', 'Operations', 'Engineering']),
            'position' => fake()->jobTitle(),
            'role' => UserRole::EMPLOYEE, 
            'profile_photo_path' => null,
            'remember_token' => Str::random(10),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Factory States
    |--------------------------------------------------------------------------
    */

    /**
     * Indicate that the user is an Administrator.
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => UserRole::ADMIN,
            'department' => 'Administration',
            'position' => 'System Administrator',
            // Enforce a specific ID pattern for Admins
            'employee_id' => 'ADM-' . fake()->unique()->numerify('####'),
        ]);
    }

    /**
     * Indicate that the user belongs to a specific department.
     */
    public function inDepartment(string $department): static
    {
        return $this->state(fn (array $attributes) => [
            'department' => $department,
        ]);
    }

    /**
     * Indicate that the user's email is unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
