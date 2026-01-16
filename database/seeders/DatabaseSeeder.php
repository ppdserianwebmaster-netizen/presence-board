<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Movement;
use App\Imports\UsersImport;
use App\Enums\MovementType;
use App\Enums\UserRole;
use Maatwebsite\Excel\Facades\Excel;

class DatabaseSeeder extends Seeder
{
    private const USE_EXCEL_IMPORT = true;
    private const EXCEL_FILE_PATH = 'employees.xlsx';

    public function run(): void
    {
        $this->command->info('🌱 Starting Database Seeding...');

        // 1. Create Admin
        User::factory()->admin()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
        ]);

        // 2. Create Employees
        if (self::USE_EXCEL_IMPORT && file_exists(storage_path('app/' . self::EXCEL_FILE_PATH))) {
            Excel::import(new UsersImport, storage_path('app/' . self::EXCEL_FILE_PATH));
        } else {
            User::factory()->count(50)->create();
        }

        $employees = User::employee()->get();

        // 3. Create Movements using Factory States
        if ($employees->isNotEmpty()) {
            // Bulk Random Movements
            Movement::factory()->count(30)->past()->create(['user_id' => fn() => $employees->random()->id]);
            Movement::factory()->count(40)->active()->create(['user_id' => fn() => $employees->random()->id]);
            Movement::factory()->count(10)->future()->create(['user_id' => fn() => $employees->random()->id]);

            // Specific Scenarios
            Movement::factory()->count(5)->create([
                'user_id' => fn() => $employees->random()->id,
                'type'    => MovementType::LEAVE,
                'ended_at' => null,
                'remark'  => 'Medical Leave - TBD',
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
                ['Currently Away', Movement::active()->count()],
            ]
        );
        $this->command->info('✅ Seeding Completed!');
    }
}
