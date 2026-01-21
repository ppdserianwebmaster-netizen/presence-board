<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Movement;
use App\Imports\UsersImport;
use App\Enums\MovementType;
use Maatwebsite\Excel\Facades\Excel;

class DatabaseSeeder extends Seeder
{
    /**
     * PHP 8.4: Constants can now have type hints!
     */
    private const bool USE_EXCEL_IMPORT = true;
    private const string EXCEL_FILE_PATH = 'employees.xlsx';

    public function run(): void
    {
        $this->command->info('🌱 Starting Database Seeding...');

        // 1. Create Admin (Primary Account)
        User::factory()->admin()->create([
            'name'  => 'Admin User',
            'email' => 'admin@example.com',
            'password' => 'admin123', // Model cast handles the hashing
        ]);

        // 2. Hydrate Employee Base
        $fullPath = storage_path('app/' . self::EXCEL_FILE_PATH);
        
        if (self::USE_EXCEL_IMPORT && file_exists($fullPath)) {
            $this->command->comment('📊 Importing from Excel...');
            Excel::import(new UsersImport, $fullPath);
        } else {
            $this->command->comment('🎲 Generating random employees (Faker)...');
            User::factory()->count(50)->create();
        }

        // Fetch only employees for movement assignment
        $employees = User::employee()->get();

        // 3. Generate Movement History
        if ($employees->isNotEmpty()) {
            $this->command->comment('🏃 Generating movement history...');

            // Past History (Completed records)
            Movement::factory()
                ->count(100)
                ->recycle($employees)
                ->past()
                ->create();

            // Active States (Showing on the Livewire Dashboard)
            Movement::factory()
                ->count(15)
                ->recycle($employees)
                ->active()
                ->create();

            // Long-term/Indefinite Scenarios (Medical Leave/Travel)
            Movement::factory()
                ->count(5)
                ->recycle($employees)
                ->create([
                    'type'       => MovementType::LEAVE,
                    'started_at' => now()->subDays(2),
                    'ended_at'   => null,
                    'remark'     => 'Extended Medical Leave',
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
                ['Active (Away Now)', Movement::active()->count()],
            ]
        );
        $this->command->info('✅ Seeding Completed!');
    }
}
