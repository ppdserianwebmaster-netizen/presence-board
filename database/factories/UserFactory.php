<?php

namespace Database\Factories;

use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => 'password', // Auto-hashed by Model cast
            'employee_id' => $this->faker->unique()->numerify('EMP####'),
            'department' => $this->faker->randomElement(['IT', 'HR', 'Finance', 'Admin', 'Sales']),
            'position' => $this->faker->jobTitle(),
            'role' => UserRole::EMPLOYEE,
            'profile_photo_path' => null,
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * State: Administrator
     */
    public function admin(): static
    {
        return $this->state(fn () => [
            'role' => UserRole::ADMIN,
            'employee_id' => 'ADMIN001',
            'department' => 'Administration',
            'position' => 'System Administrator',
        ]);
    }
}
