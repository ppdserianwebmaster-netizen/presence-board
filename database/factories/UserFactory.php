<?php
// database/factories/UserFactory.php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * User Factory (Laravel 12)
 *
 * Generates realistic User records for:
 * - Database seeding
 * - Automated tests
 * - Local development environments
 *
 * This factory aligns with the application's
 * employee-based user model and role system.
 *
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * Explicit declaration improves IDE support
     * and future refactor safety.
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * This represents a standard employee account
     * with verified email and realistic metadata.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            /*
             |------------------------------------------------------------------
             | Identity & Authentication
             |------------------------------------------------------------------
             */
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),

            /**
             * Password is intentionally stored as plain text here.
             * The User model is responsible for hashing via mutator
             * or casting, ensuring consistency across the app.
             */
            'password' => 'password',

            /*
             |------------------------------------------------------------------
             | Employee Metadata
             |------------------------------------------------------------------
             */
            'employee_id' => $this->faker->unique()->numerify('EMP####'),
            'department' => $this->faker->randomElement([
                'IT',
                'Human Resources',
                'Finance',
                'Administration',
                'Sales',
                'Marketing',
                'Operations',
            ]),
            'position' => $this->faker->jobTitle(),

            /*
             |------------------------------------------------------------------
             | Authorization
             |------------------------------------------------------------------
             */
            'role' => User::ROLE_EMPLOYEE,

            /*
             |------------------------------------------------------------------
             | Optional / System Fields
             |------------------------------------------------------------------
             */
            'profile_photo_path' => null,
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Admin state.
     *
     * Produces a deterministic administrator account
     * suitable for development and testing.
     */
    public function admin(): static
    {
        return $this->state(fn () => [
            'role' => User::ROLE_ADMIN,
            'employee_id' => 'ADMIN001',
            'department' => 'Administration',
            'position' => 'System Administrator',
        ]);
    }
}
