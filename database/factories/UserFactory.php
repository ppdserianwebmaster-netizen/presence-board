<?php
// database\factories\UserFactory.php

namespace Database\Factories;

use App\Models\User;
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
            'password' => 'password', // Laravel 12 auto-hashed via model cast
            'employee_id' => $this->faker->unique()->numerify('EMP####'), // e.g. EMP0001
            'department' => $this->faker->randomElement(['IT', 'HR', 'Finance', 'Admin', 'Sales']),
            'position' => $this->faker->jobTitle(),
            'role' => User::ROLE_EMPLOYEE,
            'profile_photo_path' => null,
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * State: Admin user
     */
    public function admin(): self
    {
        return $this->state(function () {
            return [
                'role' => User::ROLE_ADMIN,
                'employee_id' => 'ADMIN001',
            ];
        });
    }
}
