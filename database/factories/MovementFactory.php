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

    public function definition(): array
    {
        $state = $this->faker->randomElement([
            ...array_fill(0, 5, 'active'),
            ...array_fill(0, 3, 'past'),
            ...array_fill(0, 2, 'future'),
        ]);

        [$startedAt, $endedAt] = $this->generateDatetimesForState($state);

        return [
            'user_id' => User::factory(),
            'type' => $this->faker->randomElement(MovementType::cases()),
            'started_at' => $startedAt,
            'ended_at' => $endedAt,
            'remark' => $this->faker->optional(0.6)->sentence(),
        ];
    }

    private function generateDatetimesForState(string $state): array
    {
        return match ($state) {
            'past'   => [
                $s = Carbon::instance($this->faker->dateTimeBetween('-60 days', '-2 days')),
                Carbon::instance($this->faker->dateTimeBetween($s, '-1 day'))
            ],
            'active' => [
                Carbon::instance($this->faker->dateTimeBetween('-7 days', 'now')),
                $this->faker->boolean(70) ? Carbon::instance($this->faker->dateTimeBetween('now', '+14 days')) : null
            ],
            'future' => [
                $s = Carbon::instance($this->faker->dateTimeBetween('+1 day', '+30 days')),
                $this->faker->boolean(80) ? Carbon::instance($this->faker->dateTimeBetween($s, '+60 days')) : null
            ],
            default  => [now(), null],
        };
    }

    // Explicit states for manual seeding
    public function active(): static { return $this->state(fn() => ['started_at' => now()->subDay(), 'ended_at' => now()->addDay()]); }
    public function past(): static { return $this->state(fn() => ['started_at' => now()->subDays(10), 'ended_at' => now()->subDays(8)]); }
    public function future(): static { return $this->state(fn() => ['started_at' => now()->addDays(5), 'ended_at' => now()->addDays(7)]); }
    public function returningToday(): static { return $this->state(fn() => ['started_at' => now()->subHours(2), 'ended_at' => now()->addHours(2)]); }
}
