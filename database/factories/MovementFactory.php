<?php

namespace Database\Factories;

use App\Models\Movement;
use App\Models\User;
use App\Enums\MovementType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class MovementFactory extends Factory
{
    protected $model = Movement::class;

    /**
     * Default state using your probability distribution logic.
     */
    public function definition(): array
    {
        // Probability distribution for general seeding
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
    | Explicit States (Required by DatabaseSeeder)
    |--------------------------------------------------------------------------
    */

    /**
     * Specifically forces a 'Past' record.
     */
    public function past(): static
    {
        return $this->state(fn() => [
            'started_at' => $s = now()->subDays(rand(5, 20)),
            'ended_at'   => (clone $s)->addDays(rand(1, 3)),
        ]);
    }

    /**
     * Specifically forces an 'Active' record (Visible on Dashboard).
     */
    public function active(): static 
    {
        return $this->state(fn() => [
            'started_at' => now()->subHours(rand(1, 12)), 
            'ended_at'   => fake()->boolean(80) ? now()->addHours(rand(1, 12)) : null
        ]);
    }

    /**
     * Specifically forces a 'Future' record.
     */
    public function future(): static
    {
        return $this->state(fn() => [
            'started_at' => $s = now()->addDays(rand(2, 5)),
            'ended_at'   => (clone $s)->addDays(rand(1, 5)),
        ]);
    }

    /**
     * Helpers for specific scenario testing.
     */
    public function returningToday(): static 
    {
        return $this->state(fn() => [
            'started_at' => now()->subHours(2), 
            'ended_at'   => now()->addHours(2)
        ]);
    }

    public function type(MovementType $type): static
    {
        return $this->state(fn() => [
            'type' => $type,
        ]);
    }
}
