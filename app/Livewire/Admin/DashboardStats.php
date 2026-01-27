<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Models\Movement;
use App\Enums\MovementType;
use Livewire\Component;
use Illuminate\Support\Collection;

/**
 * DashboardStats Component
 * Handles the high-level metrics for the Admin Overview.
 */
class DashboardStats extends Component
{
    /**
     * Render the dashboard statistics.
     * Optimized for PHP 8.4 and Laravel 12 performance.
     */
    public function render()
    {
        // 1. Get counts directly from the database (Performance Optimized)
        // Instead of fetching all movements, we let SQL do the counting.
        $activeMovementCounts = Movement::active()
            ->select('type')
            ->selectRaw('count(*) as total')
            ->groupBy('type')
            ->pluck('total', 'type');

        // 2. Map Enum cases to include counts and UI metadata
        $typeStats = collect(MovementType::cases())->map(fn (MovementType $type) => [
            'label' => $type->label(),
            // Match the Enum value against the database result keys
            'count' => $activeMovementCounts[$type->value] ?? 0,
            'color' => $type->color(),
            'icon'  => $type->icon(),
        ]);

        return view('livewire.admin.dashboard-stats', [
            // Use User model scope for employees
            'totalEmployees' => User::employee()->count(),
            
            // Total sum of all active movements
            'totalOutNow'    => $activeMovementCounts->sum(),
            
            'typeCounts'     => $typeStats,
            
            // Eager load only necessary user fields for activity feed
            'recentActivity' => Movement::query()
                ->with('user:id,name,employee_id')
                ->latest('started_at')
                ->take(5)
                ->get(),
        ]);
    }
}
