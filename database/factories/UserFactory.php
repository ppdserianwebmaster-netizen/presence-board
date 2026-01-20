<?php
// database\factories\UserFactory.php

namespace Database\Factories;

use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * User Factory - Refactored for Laravel 12
 */
class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => 'password', // Casts in User.php will handle hashing
            'employee_id' => fake()->unique()->bothify('EMP-####-??'), // e.g. EMP-1234-XY
            'department' => fake()->randomElement(['IT', 'HR', 'Finance', 'Admin', 'Operations']),
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
     * Create a System Administrator.
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => UserRole::ADMIN,
            'department' => 'Administration',
            'position' => 'System Administrator',
            'employee_id' => 'ADMIN-' . fake()->unique()->numerify('###'),
        ]);
    }

    /**
     * Create a user with a specific department.
     */
    public function inDepartment(string $department): static
    {
        return $this->state(fn (array $attributes) => [
            'department' => $department,
        ]);
    }

    /**
     * Set email as unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
