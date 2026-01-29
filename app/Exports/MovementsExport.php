<?php

namespace App\Exports;

use App\Models\Movement;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class MovementsExport implements FromQuery, WithMapping, WithHeadings, ShouldAutoSize
{
    /**
     * PHP 8.x Constructor Property Promotion.
     * We use a nullable string for the month filter (expected format: 'YYYY-MM').
     */
    public function __construct(
        protected ?string $month = null
    ) {}

    /**
     * Prepare the query for the export.
     * Refactored to use arrow functions and better date handling.
     */
    public function query(): Builder
    {
        return Movement::query()
            // Using withTrashed() ensures we don't get 'Deleted User' errors if the relation is missing
            ->with(['user' => fn($query) => $query->withTrashed()])
            ->when($this->month, function (Builder $query, string $month) {
                $date = Carbon::parse($month);
                $query->whereMonth('started_at', $date->month)
                      ->whereYear('started_at', $date->year);
            })
            ->latest('started_at');
    }

    /**
     * Define the header row.
     */
    public function headings(): array
    {
        return [
            'Employee ID',
            'Employee Name',
            'Type',
            'Start Time',
            'End Time',
            'Remark',
        ];
    }

    /**
     * Map each row of the database to the excel columns.
     * Optimized with PHP 8.x null-safe operators and cleaner fallbacks.
     *
     * @param Movement $movement
     */
    public function map($movement): array
    {
        return [
            $movement->user?->employee_id ?? 'N/A',
            $movement->user?->name ?? 'Deleted User',
            $movement->type->label(), // Assuming 'type' is a backed enum
            $movement->started_at?->format('d/m/Y H:i') ?? '-',
            $movement->ended_at?->format('d/m/Y H:i') ?? 'Ongoing',
            $movement->remark ?? '-',
        ];
    }
}
