<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Movement;
use App\Imports\UsersImport;
use App\Enums\MovementType;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Database Seeder - Optimized for Laravel 12 & PHP 8.4
 * Orchestrates the hydration of the system with realistic data.
 */
class DatabaseSeeder extends Seeder
{
    /**
     * PHP 8.4: Typed Constants
     */
    private const bool USE_EXCEL_IMPORT = true;
    private const string EXCEL_FILE_NAME = 'employees.xlsx';

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🌱 Starting Database Seeding...');

        // 1. Create Default Admin
        // This ensures you always have a login after a fresh migration.
        User::factory()->admin()->create([
            'name'     => 'System Admin',
            'email'    => 'admin@example.com',
            'password' => 'admin123', // Model cast handles hashing
        ]);

        // 2. Populate Employee Data
        $this->hydrateEmployees();

        // 3. Generate Movement History for Employees
        $this->hydrateMovements();

        $this->displaySummary();
    }

    /**
     * Handles the creation of users via Excel or Factory.
     */
    private function hydrateEmployees(): void
    {
        // Search in local storage/app/
        $excelPath = storage_path('app/' . self::EXCEL_FILE_NAME);

        if (self::USE_EXCEL_IMPORT && file_exists($excelPath)) {
            $this->command->comment('📊 Importing employees from Excel...');
            Excel::import(new UsersImport, $excelPath);
        } else {
            $this->command->warn('⚠️ Excel file not found or import disabled. Using Factory...');
            User::factory()->count(50)->create();
        }
    }

    /**
     * Creates a mix of past, current, and future movements.
     */
    private function hydrateMovements(): void
    {
        $employees = User::employee()->get();

        if ($employees->isEmpty()) {
            $this->command->error('❌ No employees found to assign movements to.');
            return;
        }

        $this->command->comment('🏃 Generating movement history...');

        // Past Records (Already returned)
        Movement::factory()
            ->count(100)
            ->recycle($employees)
            ->past()
            ->create();

        // Active Records (Currently "Out")
        Movement::factory()
            ->count(12)
            ->recycle($employees)
            ->active()
            ->create();

        // Specific Scenarios for Dashboard Testing
        Movement::factory()
            ->count(3)
            ->recycle($employees)
            ->create([
                'type'       => MovementType::LEAVE,
                'started_at' => now()->startOfDay(),
                'ended_at'   => null,
                'remark'     => 'On Medical Leave (Seeded)',
            ]);
    }

    /**
     * Renders a clean table summary in the CLI.
     */
    private function displaySummary(): void
    {
        $this->command->newLine();
        $this->command->table(
            ['Entity', 'Total Records'],
            [
                ['Users (Total)', User::count()],
                ['Users (Employees)', User::employee()->count()],
                ['Movements (Total)', Movement::count()],
                ['Movements (Active Now)', Movement::active()->count()],
            ]
        );
        $this->command->info('✅ Seeding completed successfully.');
    }
}
