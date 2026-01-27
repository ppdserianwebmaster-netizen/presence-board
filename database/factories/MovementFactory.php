<?php

namespace Database\Factories;

use App\Models\Movement;
use App\Models\User;
use App\Enums\MovementType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * Movement Factory - Optimized for Laravel 12 & PHP 8.4
 * Simulates realistic personnel movements for dashboard testing.
 */
class MovementFactory extends Factory
{
    protected $model = Movement::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        // 8.4-style array unpacking for probability logic
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
            'remark'     => fake()->optional(0.7)->sentence(),
        ];
    }

    /**
     * Generate chronologically valid movements.
     * Refactored to prevent 'Start date must be anterior to end date' error.
     */
    private function generateDatetimesForState(string $state): array
    {
        return match ($state) {
            'past' => [
                $s = Carbon::instance(fake()->dateTimeBetween('-60 days', '-2 days')),
                // Ensure end date is at least 1 hour AFTER start date
                Carbon::instance(fake()->dateTimeBetween($s->copy()->addHour(), $s->copy()->addDays(2)))
            ],
            
            'active' => [
                Carbon::instance(fake()->dateTimeBetween('-12 hours', 'now')),
                // 70% chance of having a scheduled end time in the near future
                fake()->boolean(70) ? Carbon::instance(fake()->dateTimeBetween('+1 minute', '+8 hours')) : null
            ],
            
            'future' => [
                // Start is at least 1 day from now
                $s = Carbon::instance(fake()->dateTimeBetween('+1 day', '+10 days')),
                // End is at least 1 hour after the generated start
                fake()->boolean(90) ? Carbon::instance(fake()->dateTimeBetween($s->copy()->addHour(), $s->copy()->addDays(5))) : null
            ],
            
            default => [now(), null],
        };
    }

    /*
    |--------------------------------------------------------------------------
    | Factory States
    |--------------------------------------------------------------------------
    */

    public function past(): static
    {
        return $this->state(fn() => [
            'started_at' => $s = now()->subDays(rand(5, 30)),
            'ended_at'   => $s->copy()->addHours(rand(1, 48)),
        ]);
    }

    public function active(): static 
    {
        return $this->state(fn() => [
            'started_at' => now()->subHours(rand(1, 5)), 
            'ended_at'   => null, // Strictly "In Progress"
        ]);
    }

    public function type(MovementType $type): static
    {
        return $this->state(['type' => $type]);
    }
}
