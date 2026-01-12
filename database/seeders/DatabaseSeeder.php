<?php
// database\seeders\DatabaseSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Movement;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ----------------------
        // 1. Create Admin
        // ----------------------
        User::factory()->admin()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
        ]);

        // ----------------------
        // 2. Create 50 Employees
        // ----------------------
        $employees = User::factory()->count(50)->create();

        // ----------------------
        // 3. Create 100 Movements randomly for employees
        // ----------------------
        $allEmployees = User::employee()->get();

        Movement::factory()
            ->count(100)
            ->make() // create in memory first
            ->each(function ($movement) use ($allEmployees) {
                // Assign a random employee for each movement
                $movement->user_id = $allEmployees->random()->id;
                $movement->save();
            });
    }
}
