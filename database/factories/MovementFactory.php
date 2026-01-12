<?php
// database\factories\MovementFactory.php

namespace Database\Factories;

use App\Models\Movement;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MovementFactory extends Factory
{
    protected $model = Movement::class;

    public function definition(): array
    {
        // Pick a random employee user (exclude admin)
        $user = User::employee()->inRandomOrder()->first();

        // Random movement type
        $type = $this->faker->randomElement(array_keys(Movement::TYPES));

        // Determine status randomly
        $status = $this->faker->randomElement([
            Movement::STATUS_PLANNED,
            Movement::STATUS_ACTIVE,
            Movement::STATUS_COMPLETED,
            Movement::STATUS_CANCELLED,
        ]);

        // Set realistic start/end datetime based on status
        switch ($status) {
            case Movement::STATUS_COMPLETED:
                $startedAt = $this->faker->dateTimeBetween('-30 days', '-1 days');
                $endedAt = $this->faker->dateTimeBetween($startedAt, '-1 days');
                break;

            case Movement::STATUS_ACTIVE:
                $startedAt = $this->faker->dateTimeBetween('-1 days', 'now');
                $endedAt = $this->faker->optional()->dateTimeBetween('now', '+1 days');
                break;

            case Movement::STATUS_PLANNED:
                $startedAt = $this->faker->dateTimeBetween('+1 days', '+30 days');
                $endedAt = $this->faker->optional()->dateTimeBetween($startedAt, '+30 days');
                break;

            case Movement::STATUS_CANCELLED:
                $startedAt = $this->faker->dateTimeBetween('-15 days', '+15 days');
                $endedAt = $this->faker->optional()->dateTimeBetween($startedAt, '+15 days');
                break;

            default:
                $startedAt = now();
                $endedAt = null;
                break;
        }

        return [
            'user_id' => $user->id,
            'movement_type' => $type,
            'status' => $status,
            'started_at' => $startedAt,
            'ended_at' => $endedAt,
            'remark' => $this->faker->optional()->sentence(),
        ];
    }

    /**
     * Optional states for convenience
     */
    public function completed(): self
    {
        return $this->state(fn() => [
            'status' => Movement::STATUS_COMPLETED,
            'started_at' => $this->faker->dateTimeBetween('-30 days', '-1 days'),
            'ended_at' => $this->faker->dateTimeBetween('-29 days', '-1 days'),
        ]);
    }

    public function active(): self
    {
        return $this->state(fn() => [
            'status' => Movement::STATUS_ACTIVE,
            'started_at' => $this->faker->dateTimeBetween('-1 days', 'now'),
            'ended_at' => $this->faker->optional()->dateTimeBetween('now', '+1 days'),
        ]);
    }

    public function planned(): self
    {
        return $this->state(fn() => [
            'status' => Movement::STATUS_PLANNED,
            'started_at' => $this->faker->dateTimeBetween('+1 days', '+30 days'),
            'ended_at' => $this->faker->optional()->dateTimeBetween('+2 days', '+30 days'),
        ]);
    }

    public function cancelled(): self
    {
        return $this->state(fn() => [
            'status' => Movement::STATUS_CANCELLED,
            'started_at' => $this->faker->dateTimeBetween('-15 days', '+15 days'),
            'ended_at' => $this->faker->optional()->dateTimeBetween('started_at', '+15 days'),
        ]);
    }
}
