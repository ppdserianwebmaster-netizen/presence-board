<?php
// database/factories/MovementFactory.php

namespace Database\Factories;

use App\Models\Movement;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Movement Factory (Laravel 12)
 *
 * Generates realistic movement records using a
 * datetime-derived status model (no status column).
 *
 * Temporal States:
 * - past   : started_at < now && ended_at < now
 * - active : started_at <= now && (ended_at > now || ended_at IS NULL)
 * - future : started_at > now
 *
 * This factory is optimized for:
 * - Presence board testing
 * - Edge-case date logic validation
 * - Deterministic state-based seeding
 *
 * @extends Factory<Movement>
 */
class MovementFactory extends Factory
{
    /**
     * The factory's corresponding model.
     */
    protected $model = Movement::class;

    /**
     * Define the model's default state.
     *
     * Distribution:
     * - 50% active movements
     * - 30% past movements
     * - 20% future movements
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        /*
         |------------------------------------------------------------------
         | User Association
         |------------------------------------------------------------------
         | Automatically assigns a random employee unless overridden
         | via ->for(User::factory()) or ->for($user).
         */
        $user = User::employee()->inRandomOrder()->first();

        /*
         |------------------------------------------------------------------
         | Temporal State Selection
         |------------------------------------------------------------------
         */
        $temporalState = $this->faker->randomElement([
            'active', 'active', 'active', 'active', 'active', // 50%
            'past', 'past', 'past',                         // 30%
            'future', 'future',                             // 20%
        ]);

        [$startedAt, $endedAt] = $this->generateDatetimesForState($temporalState);

        return [
            'user_id' => $user?->id ?? User::factory(),
            'movement_type' => $this->faker->randomElement(array_keys(Movement::TYPES)),
            'started_at' => $startedAt,
            'ended_at' => $endedAt,
            'remark' => $this->faker->optional(0.6)->sentence(),
        ];
    }

    /**
     * Generate start/end datetimes based on a temporal state.
     *
     * @param  string  $state  past | active | future
     * @return array{Carbon, Carbon|null}
     */
    private function generateDatetimesForState(string $state): array
    {
        return match ($state) {
            'past' => $this->pastRange(),
            'active' => $this->activeRange(),
            'future' => $this->futureRange(),
            default => [now(), null],
        };
    }

    /**
     * Past movement range.
     */
    private function pastRange(): array
    {
        $startedAt = Carbon::instance($this->faker->dateTimeBetween('-60 days', '-2 days'));
        $endedAt = Carbon::instance($this->faker->dateTimeBetween($startedAt, '-1 day'));

        return [$startedAt, $endedAt];
    }

    /**
     * Active movement range.
     */
    private function activeRange(): array
    {
        $startedAt = Carbon::instance($this->faker->dateTimeBetween('-7 days', 'now'));
        $endedAt = $this->faker->optional(0.7)->passthrough(
            Carbon::instance($this->faker->dateTimeBetween('now', '+14 days'))
        );

        return [$startedAt, $endedAt];
    }

    /**
     * Future movement range.
     */
    private function futureRange(): array
    {
        $startedAt = Carbon::instance($this->faker->dateTimeBetween('+1 day', '+30 days'));
        $endedAt = $this->faker->optional(0.8)->passthrough(
            Carbon::instance($this->faker->dateTimeBetween($startedAt, '+60 days'))
        );

        return [$startedAt, $endedAt];
    }

    /*
     |======================================================================
     | Explicit Factory States
     |======================================================================
     */

    /** Past (completed) movement. */
    public function past(): static
    {
        [$startedAt, $endedAt] = $this->pastRange();

        return $this->state(fn () => compact('startedAt', 'endedAt'))
            ->state([
                'started_at' => $startedAt,
                'ended_at' => $endedAt,
            ]);
    }

    /** Active (current) movement. */
    public function active(): static
    {
        [$startedAt, $endedAt] = $this->activeRange();

        return $this->state([
            'started_at' => $startedAt,
            'ended_at' => $endedAt,
        ]);
    }

    /** Future (planned) movement. */
    public function future(): static
    {
        [$startedAt, $endedAt] = $this->futureRange();

        return $this->state([
            'started_at' => $startedAt,
            'ended_at' => $endedAt,
        ]);
    }

    /** Indefinite movement (no end date). */
    public function indefinite(): static
    {
        return $this->state([
            'started_at' => Carbon::instance($this->faker->dateTimeBetween('-7 days', 'now')),
            'ended_at' => null,
        ]);
    }

    /** Movement returning later today. */
    public function returningToday(): static
    {
        $now = now();

        return $this->state([
            'started_at' => $now->copy()->subDays(2),
            'ended_at' => $now->copy()->addHours(rand(1, 8)),
        ]);
    }

    /** Movement returning tomorrow. */
    public function returningTomorrow(): static
    {
        $tomorrow = now()->addDay();

        return $this->state([
            'started_at' => now()->subDays(2),
            'ended_at' => $tomorrow->copy()->setTime(rand(9, 17), 0),
        ]);
    }

    /** Force a specific movement type. */
    public function ofType(string $type): static
    {
        return $this->state(['movement_type' => $type]);
    }

    /** Short-duration meeting. */
    public function meeting(): static
    {
        $startedAt = Carbon::instance($this->faker->dateTimeBetween('-2 days', '+2 days'));

        return $this->state([
            'movement_type' => Movement::TYPE_MEETING,
            'started_at' => $startedAt,
            'ended_at' => $startedAt->copy()->addHours(rand(1, 4)),
            'remark' => $this->faker->randomElement([
                'Client meeting',
                'Team standup',
                'Board meeting',
                'Strategy session',
                'Project review',
            ]),
        ]);
    }

    /** Long-duration leave. */
    public function leave(): static
    {
        $startedAt = Carbon::instance($this->faker->dateTimeBetween('-7 days', '+7 days'));

        return $this->state([
            'movement_type' => Movement::TYPE_LEAVE,
            'started_at' => $startedAt,
            'ended_at' => $this->faker->optional(0.7)->passthrough(
                $startedAt->copy()->addDays(rand(3, 14))
            ),
            'remark' => $this->faker->randomElement([
                'Annual leave',
                'Medical leave',
                'Emergency leave',
                'Paternity leave',
                'Maternity leave',
            ]),
        ]);
    }
}
