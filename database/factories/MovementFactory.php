<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Movement;
use App\Models\User;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Movement>
 */
class MovementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = fake()->randomElement(array_keys(Movement::TYPES));
        $dateAnchor = Carbon::now()->startOfDay()->addDays(fake()->numberBetween(-5,5));

        $isShort = fake()->boolean(30);
        if($isShort){
            $startHour = fake()->numberBetween(8,15);
            $start = $dateAnchor->copy()->setHour($startHour)->setMinute(0);
            $end = $start->copy()->addHours(fake()->numberBetween(1,3));
        } else {
            $durationDays=fake()->numberBetween(0,4);
            $start=$dateAnchor->copy()->setHour(8)->setMinute(0);
            $end=$dateAnchor->copy()->addDays($durationDays)->setHour(17)->setMinute(0);
        }

        $status=Movement::STATUS_PLANNED;
        $now=now();
        
        // Logic to determine status based on time relative to now
        if($end->isPast()) {
            $status = Movement::STATUS_COMPLETED;
        } elseif($start->isPast() && $end->isFuture()) {
            $status = Movement::STATUS_ACTIVE;
        }

        // 5% chance of being cancelled regardless of time status
        if(fake()->boolean(5)) $status=Movement::STATUS_CANCELLED;

        return [
            'user_id'=>User::factory(),
            'start_datetime'=>$start,
            'end_datetime'=>$end,
            'type'=>$type,
            'note'=>fake()->sentence(4),
            'status'=>$status,
        ];
    }
    
    /**
     * Define a state for a movement that is currently active with no set end date.
     */
    public function ongoing(): static
    {
        // Start date is in the past (1 to 10 days ago)
        $start = Carbon::now()->subDays(fake()->numberBetween(1, 10))->setHour(9);
        
        return $this->state(fn(array $attrs)=>[
            'start_datetime' => $start,
            'end_datetime' => null, // Explicitly set to NULL to test the nullable column
            'type' => Movement::TYPE_LEAVE, // Often ongoing movements are leave or travel
            'status' => Movement::STATUS_ACTIVE,
            'note' => 'Indefinite status for system testing (end_datetime is NULL).',
        ]);
    }
}
