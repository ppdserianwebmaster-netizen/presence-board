<?php

namespace Database\Factories;

use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * User Factory - Refactored for Laravel 12 & PHP 8.4
 */
class UserFactory extends Factory
{
    protected $model = User::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            
            // Let the Model Cast handle the hashing
            'password' => 'password',
            
            'employee_id' => fake()->unique()->bothify('EMP-####-??'), 
            'department' => fake()->randomElement(['IT', 'HR', 'Finance', 'Admin', 'Operations']),
            'position' => fake()->jobTitle(),
            'role' => UserRole::EMPLOYEE, // Uses the Backed Enum
            'profile_photo_path' => null,
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Password caching for high-speed seeding.
     */
    protected static ?string $password;

    /*
    |--------------------------------------------------------------------------
    | Factory States
    |--------------------------------------------------------------------------
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

    public function inDepartment(string $department): static
    {
        return $this->state(fn (array $attributes) => [
            'department' => $department,
        ]);
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
