<?php

namespace App\Livewire\PublicBoard;

use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Collection;

#[Layout('components.layouts.public')]
class PresenceBoard extends Component
{
    public int $page = 1;
    public int $perPage = 8;

    private const int DATA_CACHE = 8; 
    private const int STATS_CACHE = 30;

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
        return Cache::remember('pb_total_count', self::STATS_CACHE, function () {
            return User::employee()->count();
        });
    }
    
    #[Computed]
    public function awayCount(): int 
    {
        return Cache::remember('pb_away_count', self::STATS_CACHE, function() {
            return User::employee()->whereHas('currentMovementRel')->count();
        });
    }

    #[Computed]
    public function presentCount(): int 
    { 
        return max(0, $this->totalUsers - $this->awayCount); 
    }

    public function getCardData(User $user): array
    {
        $movement = $user->current_movement;

        if (!$movement) {
            return [
                'statusColor' => '#10b981', 
                'badgeLabel'  => 'PRESENT',
                'typeLabel'   => 'In Office',
                'iconName'    => 'check-circle'
            ];
        }

        $end = $movement->ended_at;

        return match(true) {
            !$end => [
                'statusColor' => '#8b5cf6', 
                'badgeLabel'  => 'OUT',
                'typeLabel'   => $movement->type->label(),
                'iconName'    => $movement->type->icon()
            ],
            $end->isToday() => [
                'statusColor' => '#f59e0b', 
                'badgeLabel'  => 'BACK TODAY',
                'typeLabel'   => $movement->type->label(),
                'iconName'    => 'clock'
            ],
            $end->isTomorrow() => [
                'statusColor' => '#0ea5e9', 
                'badgeLabel'  => 'TOMORROW',
                'typeLabel'   => $movement->type->label(),
                'iconName'    => 'calendar'
            ],
            default => [
                'statusColor' => '#f43f5e', 
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
