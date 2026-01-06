<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $departments=['Sales','Marketing','Finance','HR','Engineering','Operations','IT Support'];
        $positions=['Manager','Senior Associate','Analyst','Director','Specialist','Trainee'];

        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            // 'two_factor_secret' => Str::random(10),
            // 'two_factor_recovery_codes' => Str::random(10),
            // 'two_factor_confirmed_at' => now(),
            'employee_id'=>'EMP'.fake()->unique()->randomNumber(4,true),
            'department'=>fake()->randomElement($departments),
            'position'=>fake()->randomElement($positions),
            'role'=>User::ROLE_EMPLOYEE,
            'profile_photo' => null,
        ];
    }

    public function administrator(): static
    {
        return $this->state(fn(array $attrs)=>[
            'role'=>User::ROLE_ADMIN,
            'employee_id'=>'ADMIN'.fake()->unique()->randomNumber(4,true),
            'department'=>'Administration',
            'position'=>'System Administrator',
        ]);
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Indicate that the model does not have two-factor authentication configured.
     */
    // public function withoutTwoFactor(): static
    // {
    //     return $this->state(fn (array $attributes) => [
    //         'two_factor_secret' => null,
    //         'two_factor_recovery_codes' => null,
    //         'two_factor_confirmed_at' => null,
    //     ]);
    // }
}
