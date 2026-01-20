<?php
// database\factories\MovementFactory.php

namespace Database\Factories;

use App\Models\Movement;
use App\Models\User;
use App\Enums\MovementType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Movement Factory
 * Optimized for realistic distribution of employee presence data.
 */
class MovementFactory extends Factory
{
    protected $model = Movement::class;

    public function definition(): array
    {
        // Probability distribution: 50% Active, 30% Past, 20% Future
        $state = fake()->randomElement([
            ...array_fill(0, 5, 'active'),
            ...array_fill(0, 3, 'past'),
            ...array_fill(0, 2, 'future'),
        ]);

        [$startedAt, $endedAt] = $this->generateDatetimesForState($state);

        return [
            'user_id'    => User::factory(),
            'type'       => fake()->randomElement(MovementType::cases()),
            'started_at' => $startedAt,
            'ended_at'   => $endedAt,
            'remark'     => fake()->optional(0.6)->sentence(),
        ];
    }

    /**
     * Logic to ensure chronologically valid movements based on the state.
     */
    private function generateDatetimesForState(string $state): array
    {
        return match ($state) {
            'past' => [
                $s = Carbon::parse(fake()->dateTimeBetween('-60 days', '-2 days')),
                Carbon::parse(fake()->dateTimeBetween($s, '-1 day'))
            ],
            'active' => [
                Carbon::parse(fake()->dateTimeBetween('-7 days', 'now')),
                fake()->boolean(70) ? Carbon::parse(fake()->dateTimeBetween('now', '+14 days')) : null
            ],
            'future' => [
                $s = Carbon::parse(fake()->dateTimeBetween('+1 day', '+30 days')),
                fake()->boolean(80) ? Carbon::parse(fake()->dateTimeBetween($s, '+60 days')) : null
            ],
            default => [now(), null],
        };
    }

    /*
    |--------------------------------------------------------------------------
    | Explicit States (Corrected Method Syntax)
    |--------------------------------------------------------------------------
    */

    public function active(): static 
    {
        return $this->state(fn() => [
            'started_at' => now()->subDay(), 
            'ended_at'   => now()->addDay()
        ]);
    }

    public function past(): static 
    {
        return $this->state(fn() => [
            'started_at' => now()->subDays(10), 
            'ended_at'   => now()->subDays(8)
        ]);
    }

    public function future(): static 
    {
        return $this->state(fn() => [
            'started_at' => now()->addDays(5), 
            'ended_at'   => now()->addDays(7)
        ]);
    }

    public function returningToday(): static 
    {
        return $this->state(fn() => [
            'started_at' => now()->subHours(2), 
            'ended_at'   => now()->addHours(2)
        ]);
    }
}
