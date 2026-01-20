<?php
// database\seeders\DatabaseSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Movement;
use App\Imports\UsersImport;
use App\Enums\MovementType;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    private const bool USE_EXCEL_IMPORT = true;
    private const string EXCEL_FILE_PATH = 'employees.xlsx';

    public function run(): void
    {
        $this->command->info('🌱 Starting Database Seeding...');

        // 1. Create Admin
        User::factory()->admin()->create([
            'name'  => 'Admin User',
            'email' => 'admin@example.com',
        ]);

        // 2. Create Employees
        // PHP 8.4 null-safe and storage check
        $fullPath = storage_path('app/' . self::EXCEL_FILE_PATH);
        
        if (self::USE_EXCEL_IMPORT && file_exists($fullPath)) {
            $this->command->comment('📊 Importing from Excel...');
            Excel::import(new UsersImport, $fullPath);
        } else {
            $this->command->comment('🎲 Generating random employees...');
            User::factory()->count(50)->create();
        }

        // Fetch employees created above
        $employees = User::employee()->get();

        // 3. Create Movements using "Recycle" for Performance
        if ($employees->isNotEmpty()) {
            $this->command->comment('🏃 Generating movement history...');

            // Use recycle() to randomly assign movements to existing employees without extra queries
            Movement::factory()
                ->count(30)
                ->recycle($employees)
                ->past()
                ->create();

            Movement::factory()
                ->count(40)
                ->recycle($employees)
                ->active()
                ->create();

            Movement::factory()
                ->count(10)
                ->recycle($employees)
                ->future()
                ->create();

            // Specific Scenarios using state overrides
            Movement::factory()
                ->count(5)
                ->recycle($employees)
                ->create([
                    'type'     => MovementType::LEAVE,
                    'ended_at' => null,
                    'remark'   => 'Medical Leave - TBD',
                ]);
        }

        $this->displaySummary();
    }

    private function displaySummary(): void
    {
        $this->command->newLine();
        $this->command->table(
            ['Metric', 'Count'],
            [
                ['Total Users', User::count()],
                ['Total Movements', Movement::count()],
                ['Active (Currently Away)', Movement::active()->count()],
            ]
        );
        $this->command->info('✅ Seeding Completed!');
    }
}
