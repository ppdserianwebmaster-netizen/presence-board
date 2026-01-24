<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Models\Movement;
use App\Enums\MovementType;
use Livewire\Component;

class DashboardStats extends Component
{
    public function render()
    {
        // 1. Get current active movements (In Progress)
        $activeMovements = Movement::active()->get();

        // 2. Map counts for each Enum type
        $typeCounts = collect(MovementType::cases())->map(function ($type) use ($activeMovements) {
            return [
                'label' => $type->label(),
                'count' => $activeMovements->where('type', $type)->count(),
                'color' => $type->color(),
                'icon'  => $type->icon(),
            ];
        });

        return view('livewire.admin.dashboard-stats', [
            'totalEmployees' => User::employee()->count(),
            'totalOutNow'    => $activeMovements->count(),
            'typeCounts'     => $typeCounts,
            'recentActivity' => Movement::with('user')->latest('started_at')->take(5)->get(),
        ]);
    }
}
