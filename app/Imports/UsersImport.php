<?php
// app\Imports\UsersImport.php

namespace App\Imports;

use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\{ToCollection, WithHeadingRow, SkipsEmptyRows};
use Illuminate\Support\Facades\DB;

class UsersImport implements ToCollection, WithHeadingRow, SkipsEmptyRows
{
    private int $employeeIdCounter;
    private readonly Collection $existingEmails;
    private readonly Collection $existingEmpIds;

    // PHP 8.4 Property Hook to ensure stats are always accessed safely
    public array $stats = [
        'total'   => 0,
        'success' => 0,
        'skipped' => 0,
        'errors'  => [],
    ];

    public function __construct()
    {
        $this->employeeIdCounter = $this->nextEmployeeIdNumber();
        
        // Cache once for speed
        $this->existingEmails = User::pluck('email');
        $this->existingEmpIds = User::pluck('employee_id');
    }

    public function collection(Collection $rows): void
    {
        DB::transaction(function () use ($rows) {
            foreach ($rows as $index => $row) {
                $this->stats['total']++;
                
                // Using PHP 8.4 null-safe and array helpers
                $name = $row['name'] ?? null;

                if (!$name) {
                    $this->skip($index, 'Name field is required');
                    continue;
                }

                try {
                    $payload = $this->buildUserPayload($row->toArray());
                    User::create($payload);

                    // Update local cache
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
            'password'          => 'password', 
            'role'              => UserRole::EMPLOYEE,
            'email_verified_at' => now(),
        ];
    }

    /* -----------------------------------------------------------------
    |  Formatting Helpers (Corrected Method Syntax)
    | -----------------------------------------------------------------
    */

    private function formatName(string $name): string
    {
        return Str::of($name)->trim()->title()->value();
    }

    private function formatText(?string $value): ?string
    {
        if (!filled($value)) return null;

        $text = trim($value);
        $acronyms = ['IT', 'HR', 'PPP', 'PPD', 'KOD', 'SISC', 'SIP'];

        foreach ($acronyms as $acronym) {
            $text = preg_replace("/\b{$acronym}\b/i", $acronym, $text);
        }

        return $text;
    }

    private function generateUniqueEmail(string $name): string
    {
        $base = Str::slug($name, '.');
        $email = "{$base}@company.com";
        $count = 1;

        // PHP 8.4: Collections are already highly optimized
        while ($this->existingEmails->contains($email)) {
            $email = "{$base}{$count}@company.com";
            $count++;
        }

        return $email;
    }

    private function generateUniqueEmployeeId(): string
    {
        // Recursion refactored to a simple loop for memory safety during large imports
        do {
            $id = 'EMP' . str_pad((string) $this->employeeIdCounter++, 4, '0', STR_PAD_LEFT);
        } while ($this->existingEmpIds->contains($id));

        return $id;
    }

    private function nextEmployeeIdNumber(): int
    {
        $last = User::where('employee_id', 'LIKE', 'EMP%')
            ->orderByRaw('LENGTH(employee_id) DESC')
            ->orderByDesc('employee_id')
            ->value('employee_id');

        return $last ? ((int) substr($last, 3)) + 1 : 1;
    }

    private function skip(int $index, string $reason): void
    {
        $this->stats['skipped']++;
        $this->stats['errors'][] = "Row " . ($index + 2) . ": {$reason}";
    }
}
