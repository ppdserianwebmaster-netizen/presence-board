<?php

namespace App\Imports;

use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\{ToCollection, WithHeadingRow, SkipsEmptyRows};
use Illuminate\Support\Facades\DB;

/**
 * UsersImport
 * * Handles bulk employee imports with automated attribute generation.
 * Optimized to reduce database round-trips using localized uniqueness checks.
 */
class UsersImport implements ToCollection, WithHeadingRow, SkipsEmptyRows
{
    private int $employeeIdCounter;
    private Collection $existingEmails;
    private Collection $existingEmpIds;

    private array $stats = [
        'total'   => 0,
        'success' => 0,
        'skipped' => 0,
        'errors'  => [],
    ];

    public function __construct()
    {
        $this->employeeIdCounter = $this->nextEmployeeIdNumber();
        
        // Performance: Cache existing values once to avoid queries inside the loop
        $this->existingEmails = User::pluck('email');
        $this->existingEmpIds = User::pluck('employee_id');
    }

    /**
     * @param Collection<int, array<string, mixed>> $rows
     */
    public function collection(Collection $rows): void
    {
        // Use a Transaction for data integrity
        DB::transaction(function () use ($rows) {
            foreach ($rows as $index => $row) {
                $this->stats['total']++;
                $rowData = $row->toArray();

                if (empty($rowData['name'])) {
                    $this->skip($index, 'Name field is required');
                    continue;
                }

                try {
                    $payload = $this->buildUserPayload($rowData);
                    User::create($payload);

                    // Track newly created data locally to ensure the NEXT row is also unique
                    $this->existingEmails->push($payload['email']);
                    $this->existingEmpIds->push($payload['employee_id']);

                    $this->stats['success']++;
                } catch (\Throwable $e) {
                    $this->skip($index, $e->getMessage());
                }
            }
        });
    }

    /**
     * Build the user creation payload.
     */
    private function buildUserPayload(array $row): array
    {
        return [
            'name'              => $this->formatName($row['name']),
            'department'        => $this->formatText($row['department'] ?? null),
            'position'          => $this->formatText($row['position'] ?? null),
            'email'             => $this->generateUniqueEmail($row['name']),
            'employee_id'       => $this->generateUniqueEmployeeId(),
            'password'          => 'password', // Hashed via User Model cast
            'role'              => UserRole::EMPLOYEE,
            'email_verified_at' => now(),
        ];
    }

    /* -----------------------------------------------------------------
     |  Formatting Helpers
     | -----------------------------------------------------------------
     */

    private function formatName(string $name): string
    {
        return Str::of($name)->trim()->lower()->title()->value();
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

    /* -----------------------------------------------------------------
     |  Generators (Optimized for Collections)
     | -----------------------------------------------------------------
     */

    private function generateUniqueEmail(string $name): string
    {
        $base = Str::slug($name, '.');
        $email = "{$base}@company.com";
        $count = 1;

        // Check against local collection instead of Database
        while ($this->existingEmails->contains($email)) {
            $email = "{$base}{$count}@company.com";
            $count++;
        }

        return $email;
    }

    private function generateUniqueEmployeeId(): string
    {
        $id = 'EMP' . str_pad($this->employeeIdCounter++, 4, '0', STR_PAD_LEFT);

        // Check against local collection instead of Database
        if ($this->existingEmpIds->contains($id)) {
            return $this->generateUniqueEmployeeId();
        }

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

    public function getStats(): array
    {
        return $this->stats;
    }
}
