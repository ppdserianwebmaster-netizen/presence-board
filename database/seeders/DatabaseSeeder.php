<?php
// database/seeders/DatabaseSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Movement;
use App\Imports\UsersImport;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Main Database Seeder
 * 
 * Toggle between Excel import and Factory generation
 */
class DatabaseSeeder extends Seeder
{
    /**
     * CONFIGURATION
     * Set to true to import from Excel, false to use factories
     */
    private const USE_EXCEL_IMPORT = true;
    
    /**
     * Excel file path (relative to storage/app/)
     */
    private const EXCEL_FILE_PATH = 'employees.xlsx';

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->newLine();
        $this->command->info('🌱 Starting Database Seeding...');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

        // =====================================================================
        // 1. Create Admin User
        // =====================================================================
        
        $this->command->info('');
        $this->command->info('👤 Creating Admin User...');
        
        $admin = User::factory()->admin()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
        ]);

        $this->command->info("   ✓ Admin created: {$admin->email}");

        // =====================================================================
        // 2. Create Employee Users (Excel or Factory)
        // =====================================================================
        
        $this->command->newLine();
        
        if (self::USE_EXCEL_IMPORT) {
            $this->importFromExcel();
        } else {
            $this->generateWithFactory();
        }

        // =====================================================================
        // 3. Create Movements
        // =====================================================================
        
        $this->command->newLine();
        $this->command->info('📍 Creating Movements...');
        
        $employees = User::employee()->get();

        if ($employees->isEmpty()) {
            $this->command->warn('   ⚠ No employees found. Skipping movement creation.');
        } else {
            $this->createMovements($employees);
        }

        // =====================================================================
        // 4. Display Summary
        // =====================================================================
        
        $this->displaySummary();
    }

    /**
     * Import users from Excel file
     * 
     * @return void
     */
    private function importFromExcel(): void
    {
        $this->command->info('📥 Importing Employees from Excel...');
        
        $filePath = storage_path('app/' . self::EXCEL_FILE_PATH);

        // Check if file exists
        if (!file_exists($filePath)) {
            $this->command->error("   ✗ Excel file not found: {$filePath}");
            $this->command->warn('   ℹ Place your Excel file at: storage/app/' . self::EXCEL_FILE_PATH);
            $this->command->newLine();
            $this->command->info('   Expected Excel format:');
            $this->command->table(
                ['name', 'department', 'position'],
                [
                    ['Clement Anak Dorem', 'SEKTOR PERANCANGAN', 'Pen. Peg. Teknologi Maklumat'],
                    ['John Doe', 'SEKTOR PERANCANGAN', 'Timbalan PPD, PPP'],
                ]
            );
            return;
        }

        $this->command->info("   📄 File: " . self::EXCEL_FILE_PATH);

        // Import users
        $import = new UsersImport();
        
        try {
            Excel::import($import, $filePath);
            
            $stats = $import->getStats();
            
            $this->command->newLine();
            $this->command->info('   Import Results:');
            $this->command->table(
                ['Metric', 'Count'],
                [
                    ['Total Rows', $stats['total']],
                    ['✓ Successfully Imported', $stats['success']],
                    ['✗ Skipped/Failed', $stats['skipped']],
                ]
            );

            // Show errors if any (limit to first 5)
            if (!empty($stats['errors'])) {
                $this->command->newLine();
                $this->command->warn('   ⚠ Errors encountered:');
                $errorCount = min(5, count($stats['errors']));
                for ($i = 0; $i < $errorCount; $i++) {
                    $this->command->line('     • ' . $stats['errors'][$i]);
                }
                if (count($stats['errors']) > 5) {
                    $remaining = count($stats['errors']) - 5;
                    $this->command->line("     ... and {$remaining} more errors");
                }
            }

        } catch (\Exception $e) {
            $this->command->error('   ✗ Import failed: ' . $e->getMessage());
        }
    }

    /**
     * Generate users with factory
     * 
     * @return void
     */
    private function generateWithFactory(): void
    {
        $this->command->info('🏭 Generating Employees with Factory...');
        
        $employeeCount = 50;
        
        User::factory()->count($employeeCount)->create();
        
        $this->command->info("   ✓ Created {$employeeCount} employees");
    }

    /**
     * Create movements for employees
     * 
     * @param \Illuminate\Support\Collection $employees
     * @return void
     */
    private function createMovements($employees): void
    {
        // Past movements
        Movement::factory()
            ->count(30)
            ->past()
            ->create(['user_id' => fn() => $employees->random()->id]);
        $this->command->info('   ✓ Created 30 past movements');

        // Active movements
        Movement::factory()
            ->count(40)
            ->active()
            ->create(['user_id' => fn() => $employees->random()->id]);
        $this->command->info('   ✓ Created 40 active movements');

        // Future movements
        Movement::factory()
            ->count(20)
            ->future()
            ->create(['user_id' => fn() => $employees->random()->id]);
        $this->command->info('   ✓ Created 20 future movements');

        // Indefinite movements
        Movement::factory()
            ->count(5)
            ->indefinite()
            ->ofType(Movement::TYPE_LEAVE)
            ->create([
                'user_id' => fn() => $employees->random()->id,
                'remark' => 'Extended medical leave - TBD',
            ]);
        $this->command->info('   ✓ Created 5 indefinite movements');

        // Test cases
        Movement::factory()->returningToday()->create([
            'user_id' => $employees->random()->id,
            'remark' => 'Client meeting - back by 3pm',
        ]);
        
        Movement::factory()->returningTomorrow()->create([
            'user_id' => $employees->random()->id,
            'remark' => 'Training course',
        ]);

        $this->command->info('   ✓ Created test case movements');
    }

    /**
     * Display seeding summary
     * 
     * @return void
     */
    private function displaySummary(): void
    {
        $totalUsers = User::count();
        $totalEmployees = User::employee()->count();
        $totalMovements = Movement::count();
        $activeMovements = Movement::activeNow()->count();

        $this->command->newLine();
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->info('✅ Database Seeding Completed!');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->newLine();
        
        $this->command->table(
            ['Metric', 'Count'],
            [
                ['👥 Total Users', $totalUsers],
                ['👔 Employees', $totalEmployees],
                ['👨‍💼 Admins', User::admin()->count()],
                ['', ''],
                ['📍 Total Movements', $totalMovements],
                ['🟢 Active Now', $activeMovements],
                ['⚪ Completed', Movement::completed()->count()],
                ['🔵 Upcoming', Movement::upcoming()->where('started_at', '>', now())->count()],
            ]
        );
        
        $this->command->newLine();
        $this->command->info('🚀 Ready to use! Visit the presence board.');
        $this->command->newLine();
    }
}
