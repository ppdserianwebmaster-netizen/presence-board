<?php

namespace App\Livewire\PublicBoard;

use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Collection;

class PresenceBoard extends Component
{
    public int $page = 1;
    public int $perPage = 8;

    // Cache durations in seconds
    private const int DATA_CACHE = 8; 
    private const int STATS_CACHE = 30;

    /**
     * Rotates the page. 
     * Called by wire:poll.8s in the view.
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
        // Cache per page to prevent DB hits on every poll rotation
        return Cache::remember("pb_page_v2_{$this->page}", self::DATA_CACHE, function () {
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
        return Cache::remember('pb_total_count', self::STATS_CACHE, fn () => User::employee()->count()); 
    }
    
    #[Computed]
    public function awayCount(): int 
    {
        // Counts users currently assigned to an active movement
        return Cache::remember('pb_away_count', self::STATS_CACHE, function() {
            return User::employee()->whereHas('currentMovementRel')->count();
        });
    }

    #[Computed]
    public function presentCount(): int 
    { 
        return max(0, $this->totalUsers - $this->awayCount); 
    }

    /**
     * UI Helper: Maps status to Terminal colors and icons.
     * Updated to match the High-Contrast Black design.
     */
    public function getCardData(User $user): array
    {
        $movement = $user->current_movement;

        // 1. Default: IN OFFICE
        if (!$movement) {
            return [
                'statusColor' => '#10b981', // Emerald 500
                'badgeLabel'  => 'PRESENT',
                'typeLabel'   => 'In Office',
                'iconName'    => 'check-circle'
            ];
        }

        $end = $movement->ended_at;

        // 2. Define Away States based on the return time
        return match(true) {
            // No end date set
            !$end => [
                'statusColor' => '#8b5cf6', // Violet 500
                'badgeLabel'  => 'OUT',
                'typeLabel'   => $movement->type->label(),
                'iconName'    => $movement->type->icon()
            ],
            // Returning later today
            $end->isToday() => [
                'statusColor' => '#f59e0b', // Amber 500
                'badgeLabel'  => 'BACK TODAY',
                'typeLabel'   => $movement->type->label(),
                'iconName'    => 'clock'
            ],
            // Returning tomorrow
            $end->isTomorrow() => [
                'statusColor' => '#0ea5e9', // Sky 500
                'badgeLabel'  => 'TOMORROW',
                'typeLabel'   => $movement->type->label(),
                'iconName'    => 'calendar'
            ],
            // Away for multiple days
            default => [
                'statusColor' => '#f43f5e', // Rose 500
                'badgeLabel'  => 'AWAY',
                'typeLabel'   => $movement->type->label(),
                'iconName'    => 'plane'
            ]
        };
    }

    public function render()
    {
        return view('livewire.public-board.presence-board')
            ->layout('components.layouts.public');
    }
}
