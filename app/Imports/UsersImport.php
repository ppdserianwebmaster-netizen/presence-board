<?php

namespace App\Imports;

use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\{ToCollection, WithHeadingRow, SkipsEmptyRows};
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * UsersImport - Optimized for Laravel 12 & PHP 8.4
 * Handles bulk employee onboarding with automatic ID and Email generation.
 */
class UsersImport implements ToCollection, WithHeadingRow, SkipsEmptyRows
{
    private int $employeeIdCounter;
    private Collection $existingEmails;
    private Collection $existingEmpIds;

    /**
     * PHP 8.4 Asymmetric Visibility
     * Allows the Livewire UI to read results without being able to modify them.
     */
    public private(set) array $stats = [
        'total'   => 0,
        'success' => 0,
        'skipped' => 0,
        'errors'  => [],
    ];

    public function __construct()
    {
        $this->employeeIdCounter = $this->nextEmployeeIdNumber();
        
        // Cache once for speed using selective pluck to save XAMPP memory
        $this->existingEmails = User::pluck('email');
        $this->existingEmpIds = User::pluck('employee_id');
    }

    public function collection(Collection $rows): void
    {
        // Chunk processing to keep database transactions stable
        DB::transaction(function () use ($rows) {
            foreach ($rows as $index => $row) {
                $this->stats['total']++;
                
                $name = $row['name'] ?? null;

                if (!$name) {
                    $this->skip($index, 'The name column is empty.');
                    continue;
                }

                try {
                    $payload = $this->buildUserPayload($row->toArray());
                    
                    User::create($payload);

                    // Update local cache to prevent duplicates within the same Excel session
                    $this->existingEmails->push($payload['email']);
                    $this->existingEmpIds->push($payload['employee_id']);

                    $this->stats['success']++;
                } catch (\Throwable $e) {
                    $this->skip($index, $e->getMessage());
                }
            }
        });
    }

    private function buildUserPayload(array $row): array
    {
        return [
            'name'              => $this->formatName($row['name']),
            'department'        => $this->formatText($row['department'] ?? null),
            'position'          => $this->formatText($row['position'] ?? null),
            'email'             => $this->generateUniqueEmail($row['name']),
            'employee_id'       => $this->generateUniqueEmployeeId(),
            'password'          => 'password', // Assumes 'hashed' cast in User Model
            'role'              => UserRole::EMPLOYEE,
            'email_verified_at' => now(),
        ];
    }

    /* -----------------------------------------------------------------
    |  Formatting & Logic Helpers
    | -----------------------------------------------------------------
    */

    private function formatName(string $name): string
    {
        // PHP 8.4 String fluent interface
        return Str::of($name)->trim()->title()->value();
    }

    private function formatText(?string $value): ?string
    {
        if (!filled($value)) return null;

        $text = trim($value);
        
        /**
         * Organization Acronyms
         * Ensures these remain uppercase regardless of Excel input casing.
         */
        $acronyms = ['IT', 'HR', 'PPP', 'PPD', 'KOD', 'SISC', 'SIP', 'JPN'];

        foreach ($acronyms as $acronym) {
            // Using case-insensitive regex to fix "it" to "IT", etc.
            $text = preg_replace("/\b{$acronym}\b/i", $acronym, $text);
        }

        return $text;
    }

    private function generateUniqueEmail(string $name): string
    {
        $base = Str::slug($name, '.');
        $email = "{$base}@company.com";
        $count = 1;

        // Efficient loop to check local collection cache
        while ($this->existingEmails->contains($email)) {
            $email = "{$base}{$count}@company.com";
            $count++;
        }

        return $email;
    }

    private function generateUniqueEmployeeId(): string
    {
        do {
            $id = 'EMP' . str_pad((string) $this->employeeIdCounter++, 4, '0', STR_PAD_LEFT);
        } while ($this->existingEmpIds->contains($id));

        return $id;
    }

    private function nextEmployeeIdNumber(): int
    {
        // Find the highest numeric value at the end of the EMP string
        $last = User::where('employee_id', 'LIKE', 'EMP%')
            ->orderByRaw('LENGTH(employee_id) DESC')
            ->orderByDesc('employee_id')
            ->value('employee_id');

        return $last ? ((int) substr($last, 3)) + 1 : 1;
    }

    private function skip(int $index, string $reason): void
    {
        $this->stats['skipped']++;
        // Index + 2 accounts for 0-based array and Excel heading row
        $this->stats['errors'][] = "Row " . ($index + 2) . ": {$reason}";
    }
}
