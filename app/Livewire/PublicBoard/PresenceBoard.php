<?php

namespace App\Livewire\PublicBoard;

use App\Models\User;
use App\Models\Movement;
use App\Enums\MovementType;
use Livewire\Component;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class PresenceBoard extends Component
{
    public int $page = 1;
    public int $perPage = 8;

    private const CACHE_DURATION = 8;
    private const COUNT_CACHE_DURATION = 30;

    /**
     * Re-using the logic for active movements.
     * Removed the strict Builder type hint to prevent the TypeError.
     */
    private function activeMovementQuery($query, Carbon $now)
    {
        return $query->where('started_at', '<=', $now)
            ->where(function ($q) use ($now) {
                $q->whereNull('ended_at')->orWhere('ended_at', '>', $now);
            });
    }

    #[Computed]
    public function totalPages(): int
    {
        return Cache::remember('pb_total_pages', self::COUNT_CACHE_DURATION, function () {
            $count = User::employee()->count();
            return (int) ceil($count / $this->perPage) ?: 1;
        });
    }

    #[Computed]
    public function users(): Collection
    {
        return Cache::remember("pb_page_{$this->page}", self::CACHE_DURATION, function () {
            $now = now();
            
            $users = User::employee()
                ->with(['movements' => function ($query) use ($now) {
                    // Logic: Find current active movements
                    $this->activeMovementQuery($query, $now)
                        ->orderByRaw('ended_at IS NULL DESC')
                        ->orderBy('ended_at', 'desc')
                        ->limit(1);
                }])
                ->orderBy('name')
                ->skip(($this->page - 1) * $this->perPage)
                ->take($this->perPage)
                ->get();

            // Flatten the movement for the Blade
            $users->each(fn ($user) => $user->currentMovement = $user->movements->first());
            
            return $users;
        });
    }

    /**
     * Helper logic for the Header Stats
     */
    #[Computed]
    public function totalUsers(): int { return Cache::remember('pb_total_count', self::COUNT_CACHE_DURATION, fn () => User::employee()->count()); }
    
    #[Computed]
    public function awayCount(): int {
        $now = now();
        return User::employee()->whereHas('movements', fn ($q) => $this->activeMovementQuery($q, $now))->count();
    }

    #[Computed]
    public function presentCount(): int { return $this->totalUsers - $this->awayCount; }

    public function rotatePage(): void
    {
        $this->page = ($this->page % $this->totalPages) + 1;
        Cache::forget("pb_page_{$this->page}");
    }

    /**
     * I have restored your EXACT styling logic here.
     * Your Blade will see every single key it expects.
     */
    public function getCardData(User $user, ?Movement $movement): array
    {
        $data = [
            'statusType'  => 'present',
            'statusColor' => '#10b981',
            'borderColor' => '#059669',
            'badgeBg'     => '#064e3b',
            'badgeText'   => '#d1fae5',
            'badgeLabel'  => 'PRESENT',
            'typeLabel'   => 'In Office',
            'iconName'    => 'check-circle'
        ];

        if (!$movement) return $data;

        $data['typeLabel'] = ucfirst($movement->type->value);
        $data['iconName'] = match ($movement->type) {
            MovementType::MEETING => 'users',
            MovementType::TRAVEL  => 'plane',
            MovementType::LEAVE   => 'palmtree',
            MovementType::COURSE  => 'graduation-cap',
            MovementType::OTHER   => 'map-pin',
        };

        $end = $movement->ended_at;

        // Your exact hex codes and keys restored:
        if (!$end) {
            $data['statusType']  = 'away_indefinite';
            $data['statusColor'] = '#8b5cf6'; 
            $data['borderColor'] = '#7c3aed'; 
            $data['badgeBg']     = '#4c1d95'; 
            $data['badgeText']   = '#ede9fe'; 
            $data['badgeLabel']  = 'OUT';
        } elseif ($end->isToday()) {
            $data['statusType']  = 'back_today';
            $data['statusColor'] = '#f59e0b'; 
            $data['borderColor'] = '#d97706'; 
            $data['badgeBg']     = '#78350f'; 
            $data['badgeText']   = '#fef3c7'; 
            $data['badgeLabel']  = 'BACK TODAY';
        } elseif ($end->isTomorrow()) {
            $data['statusType']  = 'back_tomorrow';
            $data['statusColor'] = '#0ea5e9'; 
            $data['borderColor'] = '#0284c7'; 
            $data['badgeBg']     = '#082f49'; 
            $data['badgeText']   = '#e0f2fe'; 
            $data['badgeLabel']  = 'TOMORROW';
        } else {
            $data['statusType']  = 'away_long';
            $data['statusColor'] = '#f43f5e'; 
            $data['borderColor'] = '#e11d48'; 
            $data['badgeBg']     = '#881337'; 
            $data['badgeText']   = '#ffe4e6'; 
            $data['badgeLabel']  = 'AWAY';
        }

        return $data;
    }

    public function render()
    {
        return view('livewire.public-board.presence-board')
            ->layout('components.layouts.public');
    }
}
