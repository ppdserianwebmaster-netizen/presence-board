<?php
// app/Livewire/PublicBoard/PresenceBoard.php

namespace App\Livewire\PublicBoard;

use App\Models\User;
use App\Models\Movement;
use Livewire\Component;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Collection;

class PresenceBoard extends Component
{
    public int $page = 1;
    public int $perPage = 8;
    private const CACHE_DURATION = 8;

    private function activeMovementQuery($query, $now)
    {
        // A movement is ONLY active if:
        // 1. It started in the past or exactly now
        // AND 
        // 2. It hasn't ended yet (NULL) OR the end time is STILL in the future
        return $query->where('started_at', '<=', $now)
                    ->where(function($q) use ($now) {
                        $q->whereNull('ended_at')
                        ->orWhere('ended_at', '>', $now); // 14:30 is NOT > 14:40, so this returns false
                    });
    }

    #[Computed]
    public function totalPages(): int
    {
        return Cache::remember('pb_total_pages', 30, function() {
            $totalUsers = User::employee()->count();
            return max(1, (int) ceil($totalUsers / $this->perPage));
        });
    }

    #[Computed]
    public function users(): Collection
    {
        $cacheKey = "pb_page_{$this->page}";

        return Cache::remember($cacheKey, self::CACHE_DURATION, function() {
            $now = now();
            $users = User::employee()
                ->with(['movements' => function($query) use ($now) {
                    // $this->activeMovementQuery($query, $now)
                    //       ->orderBy('ended_at', 'desc')
                    //       ->limit(1);
                    $this->activeMovementQuery($query, $now)
                    // 1. Put NULLs at the top (True/1 comes before False/0 in DESC)
                    ->orderByRaw('ended_at IS NULL DESC') 
                    // 2. Then put the furthest future dates next
                    ->orderBy('ended_at', 'desc')
                    ->limit(1);
                }])
                ->orderBy('name')
                ->skip(($this->page - 1) * $this->perPage)
                ->take($this->perPage)
                ->get();

            $users->each(fn($user) => $user->currentMovement = $user->movements->first());
            return $users;
        });
    }

    public function getPresentCountProperty(): int
    {
        return $this->totalUsers - $this->awayCount;
    }

    public function getAwayCountProperty(): int
    {
        $now = now();
        return User::employee()
            ->whereHas('movements', fn($q) => $this->activeMovementQuery($q, $now))
            ->count();
    }

    public function getTotalUsersProperty(): int
    {
        return Cache::remember('pb_total_count', 30, fn() => User::employee()->count());
    }

    public function rotatePage(): void
    {
        try {
            // FIXED: Accessing as property, not method()
            $this->page = ($this->page % $this->totalPages) + 1;
            Cache::forget("pb_page_{$this->page}");
        } catch (\Exception $e) {
            $this->page = 1;
        }
    }

    public function getCardData($user, $movement): array
    {
        // DEFAULT: PRESENT (Green)
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

        // If no active movement is occurring based on the clock, return Green.
        if (!$movement) return $data;

        $end = $movement->ended_at;
        $data['typeLabel'] = Movement::TYPES[$movement->movement_type] ?? strtoupper(str_replace('_', ' ', $movement->movement_type));
        
        $data['iconName'] = match($movement->movement_type) {
            'meeting' => 'users',
            'travel'  => 'plane',
            'leave'   => 'palmtree',
            'sick'    => 'heart-pulse',
            default   => 'map-pin',
        };

        if (!$end) {
            // PURPLE: No end date set
            $data['statusType']  = 'away_indefinite';
            $data['statusColor'] = '#8b5cf6';
            $data['borderColor'] = '#7c3aed';
            $data['badgeBg']     = '#4c1d95';
            $data['badgeText']   = '#ede9fe';
            $data['badgeLabel']  = 'OUT';
        } elseif ($end->isToday()) {
            // YELLOW: Ends today but in the future (e.g., 17:00)
            $data['statusType']  = 'back_today';
            $data['statusColor'] = '#f59e0b';
            $data['borderColor'] = '#d97706';
            $data['badgeBg']     = '#78350f';
            $data['badgeText']   = '#fef3c7';
            $data['badgeLabel']  = 'BACK TODAY';
        } elseif ($end->isTomorrow()) {
            // BLUE: Ends tomorrow
            $data['statusType']  = 'back_tomorrow';
            $data['statusColor'] = '#0ea5e9';
            $data['borderColor'] = '#0284c7';
            $data['badgeBg']     = '#082f49';
            $data['badgeText']   = '#e0f2fe';
            $data['badgeLabel']  = 'TOMORROW';
        } else {
            // RED: Ends jan 15 or later
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
        return view('livewire.public-board.presence-board')->layout('components.layouts.public');
    }
}
