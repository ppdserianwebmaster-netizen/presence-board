<?php

namespace App\Livewire\PublicBoard;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.public')]
class PresenceBoard extends Component
{
    public int $page = 1;
    public int $perPage = 8;

    // PHP 8.4: Typed constants for better clarity
    private const int DATA_CACHE_SECONDS = 8; 
    private const int STATS_CACHE_SECONDS = 30;

    /**
     * Handles the auto-rotation logic for the TV dashboard.
     */
    public function rotatePage(): void
    {
        $this->page = ($this->page % $this->totalPages) + 1;
    }

    #[Computed]
    public function totalPages(): int 
    {
        return (int) ceil($this->totalUsers / $this->perPage) ?: 1;
    }

    #[Computed]
    public function users(): Collection
    {
        // Cache naming convention updated to include perPage to avoid overlap if changed
        return Cache::remember("pb_page_{$this->perPage}_{$this->page}", self::DATA_CACHE_SECONDS, function () {
            return User::employee()
                ->with(['currentMovementRel']) 
                ->orderBy('name')
                ->skip(($this->page - 1) * $this->perPage)
                ->take($this->perPage)
                ->get();
        });
    }

    #[Computed]
    public function totalUsers(): int 
    {
        return Cache::remember('pb_total_count', self::STATS_CACHE_SECONDS, fn() => User::employee()->count());
    }
    
    #[Computed]
    public function awayCount(): int 
    {
        return Cache::remember('pb_away_count', self::STATS_CACHE_SECONDS, function() {
            // Optimization: whereHas is great, but ensure index 'idx_active_status' exists
            return User::employee()->whereHas('currentMovementRel')->count();
        });
    }

    #[Computed]
    public function presentCount(): int 
    { 
        return max(0, $this->totalUsers - $this->awayCount); 
    }

    /**
     * Refactored: UI state logic for a specific user.
     * Consider moving this logic to a User property hook if used in multiple views.
     */
    public function getCardData(User $user): array
    {
        $movement = $user->current_movement;

        if (!$movement) {
            return [
                'statusColor' => '#10b981', // Tailwind green-500
                'badgeLabel'  => 'PRESENT',
                'typeLabel'   => 'In Office',
                'iconName'    => 'check-circle'
            ];
        }

        $end = $movement->ended_at;

        // PHP 8.4 match expression is perfect here
        return match(true) {
            $end === null => [
                'statusColor' => '#8b5cf6', // Tailwind violet-500
                'badgeLabel'  => 'OUT',
                'typeLabel'   => $movement->type->label(),
                'iconName'    => $movement->type->icon()
            ],
            $end->isToday() => [
                'statusColor' => '#f59e0b', // Tailwind amber-500
                'badgeLabel'  => 'BACK TODAY',
                'typeLabel'   => $movement->type->label(),
                'iconName'    => 'clock'
            ],
            $end->isTomorrow() => [
                'statusColor' => '#0ea5e9', // Tailwind sky-500
                'badgeLabel'  => 'TOMORROW',
                'typeLabel'   => $movement->type->label(),
                'iconName'    => 'calendar'
            ],
            default => [
                'statusColor' => '#f43f5e', // Tailwind rose-500
                'badgeLabel'  => 'AWAY',
                'typeLabel'   => $movement->type->label(),
                'iconName'    => 'plane'
            ]
        };
    }

    public function render()
    {
        return view('livewire.public-board.presence-board');
    }
}
