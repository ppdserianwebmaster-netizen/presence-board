<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Movement;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // 1. Create the primary Administrator account
        User::factory()->administrator()->create([
            'name'=>'Administrator',
            'email'=>'admin@example.com',
            'password'=>bcrypt('password'),
        ]);

        // 2. Create a few specific users with an ONGOING movement status for testing logic
        User::factory()->count(3)
            ->has(Movement::factory()->ongoing(), 'movements') // Use the ongoing() state
            ->create();

        // 3. Create the main batch of employees with random movements
        User::factory()->count(50)->create()->each(function($user){
            // Randomly assign between 0 and 5 movements
            $count=fake()->numberBetween(0,5);
            if($count>0) {
                Movement::factory()->count($count)->create(['user_id'=>$user->id]);
            }
        });

        // 4. Console output confirmation
        $this->command->info("✅ Users: ".User::count().", Movements: ".Movement::count());
        $this->command->info("   (Includes 3 users specifically testing 'ongoing' status)");
    }
}
