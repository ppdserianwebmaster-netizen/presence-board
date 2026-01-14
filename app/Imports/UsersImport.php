<?php
// app/Imports/UsersImport.php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\{
    ToCollection,
    WithHeadingRow,
    SkipsEmptyRows
};

/**
 * UsersImport
 *
 * Imports employees from an Excel file.
 *
 * Expected columns:
 * - name (required)
 * - department (optional)
 * - position (required)
 *
 * Other fields are generated automatically:
 * - email (unique, derived from name)
 * - employee_id (sequential EMP####)
 * - password (default, auto-hashed by model)
 *
 * Designed for:
 * - Idempotent imports
 * - Clear error reporting
 * - Laravel 12 model conventions
 */
class UsersImport implements ToCollection, WithHeadingRow, SkipsEmptyRows
{
    /**
     * Sequential counter for employee IDs.
     */
    private int $employeeIdCounter;

    /**
     * Import statistics.
     */
    private array $stats = [
        'total'   => 0,
        'success' => 0,
        'skipped' => 0,
        'errors'  => [],
    ];

    /**
     * Initialize import state.
     *
     * Determines the next available employee ID before import starts.
     */
    public function __construct()
    {
        $this->employeeIdCounter = $this->nextEmployeeIdNumber();
    }

    /**
     * Handle imported rows.
     *
     * @param Collection<int, array<string, mixed>> $rows
     */
    public function collection(Collection $rows): void
    {
        foreach ($rows as $index => $row) {
            $this->stats['total']++;

            try {
                if (!$this->hasRequiredFields($row)) {
                    $this->skip(
                        $index,
                        'Missing required fields (name or position)'
                    );
                    continue;
                }

                User::create($this->buildUserPayload($row));

                $this->stats['success']++;

            } catch (\Throwable $e) {
                $this->skip($index, $e->getMessage());
            }
        }
    }

    /* -----------------------------------------------------------------
     |  Payload builders
     | -----------------------------------------------------------------
     */

    /**
     * Build the user creation payload.
     */
    private function buildUserPayload(array $row): array
    {
        return [
            // Excel-provided fields
            'name'       => $this->formatName($row['name']),
            'department' => filled($row['department'] ?? null)
                ? $this->formatDepartment($row['department'])
                : null,
            'position'   => $this->formatPosition($row['position']),

            // Auto-generated fields
            'email'             => $this->generateEmail($row['name']),
            'employee_id'       => $this->generateEmployeeId(),
            'password'          => 'password', // auto-hashed
            'role'              => User::ROLE_EMPLOYEE,
            'email_verified_at' => now(),
            'profile_photo_path'=> null,
            'remember_token'    => Str::random(10),
        ];
    }

    /**
     * Check required fields.
     */
    private function hasRequiredFields(array $row): bool
    {
        return filled($row['name'] ?? null)
            && filled($row['position'] ?? null);
    }

    /* -----------------------------------------------------------------
     |  Formatting helpers
     | -----------------------------------------------------------------
     */

    /**
     * Normalize name casing.
     *
     * "JOHN DOE" → "John Doe"
     */
    private function formatName(string $name): string
    {
        return Str::of($name)
            ->lower()
            ->title()
            ->trim()
            ->value();
    }

    /**
     * Normalize department names while preserving acronyms.
     */
    private function formatDepartment(string $department): string
    {
        return $this->preserveAcronyms(trim($department), [
            'IT', 'HR', 'PPP', 'PPD', 'KOD', 'SISC',
        ]);
    }

    /**
     * Normalize position titles while preserving acronyms.
     */
    private function formatPosition(string $position): string
    {
        return $this->preserveAcronyms(trim($position), [
            'PPP', 'PPD', 'KOD', 'SISC', 'SIP',
        ]);
    }

    /**
     * Preserve known acronyms in strings.
     */
    private function preserveAcronyms(string $value, array $acronyms): string
    {
        foreach ($acronyms as $acronym) {
            $value = preg_replace(
                "/\b{$acronym}\b/i",
                $acronym,
                $value
            );
        }

        return $value;
    }

    /* -----------------------------------------------------------------
     |  Generators
     | -----------------------------------------------------------------
     */

    /**
     * Generate a unique email address from a name.
     *
     * Format: firstname.lastname@company.com
     */
    private function generateEmail(string $name): string
    {
        $base = Str::slug($name, '.');
        $email = "{$base}@company.com";
        $counter = 1;

        while (User::whereEmail($email)->exists()) {
            $email = "{$base}{$counter}@company.com";
            $counter++;
        }

        return $email;
    }

    /**
     * Generate the next unique employee ID.
     *
     * Format: EMP0001
     */
    private function generateEmployeeId(): string
    {
        do {
            $id = 'EMP' . str_pad(
                $this->employeeIdCounter++,
                4,
                '0',
                STR_PAD_LEFT
            );
        } while (User::whereEmployeeId($id)->exists());

        return $id;
    }

    /**
     * Determine the next employee ID number.
     */
    private function nextEmployeeIdNumber(): int
    {
        $last = User::where('employee_id', 'LIKE', 'EMP%')
            ->orderByDesc('employee_id')
            ->value('employee_id');

        return $last
            ? ((int) substr($last, 3)) + 1
            : 1;
    }

    /* -----------------------------------------------------------------
     |  Error handling & stats
     | -----------------------------------------------------------------
     */

    /**
     * Record skipped rows consistently.
     */
    private function skip(int $index, string $reason): void
    {
        $this->stats['skipped']++;
        $this->stats['errors'][] =
            'Row ' . ($index + 2) . ': ' . $reason;
    }

    /**
     * Retrieve import statistics.
     */
    public function getStats(): array
    {
        return $this->stats;
    }
}
