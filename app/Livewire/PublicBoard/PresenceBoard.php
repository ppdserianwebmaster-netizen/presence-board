<?php
// app/Livewire/PublicBoard/PresenceBoard.php

namespace App\Livewire\PublicBoard;

use App\Models\User;
use App\Models\Movement;
use Livewire\Component;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Collection;
use Carbon\Carbon;

/**
 * PresenceBoard Component
 * 
 * Displays real-time employee presence status on a public board.
 * Uses datetime-based logic to determine if employees are present or away.
 * Automatically rotates through pages every 8 seconds.
 * 
 * @package App\Livewire\PublicBoard
 */
class PresenceBoard extends Component
{
    /**
     * Current page number for pagination
     */
    public int $page = 1;

    /**
     * Number of employees displayed per page
     */
    public int $perPage = 8;

    /**
     * Cache duration in seconds for board data
     */
    private const CACHE_DURATION = 8;

    /**
     * Cache duration in seconds for total counts
     */
    private const CACHE_COUNT_DURATION = 30;

    /**
     * Determine if a movement is currently active based on datetime logic.
     * 
     * A movement is considered active when:
     * 1. It has started (started_at <= now)
     * 2. It hasn't ended yet (ended_at is NULL OR ended_at > now)
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \Carbon\Carbon $now Current datetime
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function activeMovementQuery($query, Carbon $now)
    {
        return $query->where('started_at', '<=', $now)
            ->where(function ($q) use ($now) {
                $q->whereNull('ended_at')
                    ->orWhere('ended_at', '>', $now);
            });
    }

    /**
     * Calculate total number of pages based on employee count.
     * Cached for 30 seconds to reduce database queries.
     * 
     * @return int Total number of pages
     */
    #[Computed]
    public function totalPages(): int
    {
        return Cache::remember('pb_total_pages', self::CACHE_COUNT_DURATION, function () {
            $totalUsers = User::employee()->count();
            return max(1, (int) ceil($totalUsers / $this->perPage));
        });
    }

    /**
     * Retrieve users for the current page with their active movements.
     * 
     * Performance optimizations:
     * - Cached for 8 seconds to match rotation interval
     * - Eager loads only the single most relevant movement per user
     * - Movements ordered by: NULL ended_at first, then furthest future date
     * 
     * @return \Illuminate\Support\Collection Collection of User models
     */
    #[Computed]
    public function users(): Collection
    {
        $cacheKey = "pb_page_{$this->page}";

        return Cache::remember($cacheKey, self::CACHE_DURATION, function () {
            $now = now();
            
            $users = User::employee()
                ->with(['movements' => function ($query) use ($now) {
                    $this->activeMovementQuery($query, $now)
                        // Priority 1: NULL ended_at (indefinite movements) come first
                        ->orderByRaw('ended_at IS NULL DESC')
                        // Priority 2: Then by furthest future end date
                        ->orderBy('ended_at', 'desc')
                        ->limit(1);
                }])
                ->orderBy('name')
                ->skip(($this->page - 1) * $this->perPage)
                ->take($this->perPage)
                ->get();

            // Attach the first (and only) movement as currentMovement for easy access
            $users->each(fn ($user) => $user->currentMovement = $user->movements->first());
            
            return $users;
        });
    }

    /**
     * Get the count of employees currently present in the office.
     * Present = Total employees - Away employees
     * 
     * @return int Number of present employees
     */
    public function getPresentCountProperty(): int
    {
        return $this->totalUsers - $this->awayCount;
    }

    /**
     * Get the count of employees currently away from the office.
     * Away = Has an active movement based on datetime logic
     * 
     * @return int Number of away employees
     */
    public function getAwayCountProperty(): int
    {
        $now = now();
        
        return User::employee()
            ->whereHas('movements', fn ($q) => $this->activeMovementQuery($q, $now))
            ->count();
    }

    /**
     * Get total count of all employees (not soft-deleted).
     * Cached for 30 seconds to reduce queries.
     * 
     * @return int Total number of employees
     */
    public function getTotalUsersProperty(): int
    {
        return Cache::remember('pb_total_count', self::CACHE_COUNT_DURATION, fn () => User::employee()->count());
    }

    /**
     * Rotate to the next page in the pagination cycle.
     * Called automatically every 8 seconds via wire:poll.
     * Clears cache for the next page to ensure fresh data.
     * 
     * @return void
     */
    public function rotatePage(): void
    {
        try {
            // Calculate next page (wraps around to 1 after last page)
            $this->page = ($this->page % $this->totalPages) + 1;
            
            // Pre-emptively clear cache for the next page
            Cache::forget("pb_page_{$this->page}");
        } catch (\Exception $e) {
            // Fallback to first page on any error
            $this->page = 1;
        }
    }

    /**
     * Generate card styling data based on employee's current movement status.
     * 
     * Status priority logic:
     * 1. GREEN (Present): No active movement
     * 2. PURPLE (Out Indefinite): Active movement with no end date
     * 3. YELLOW (Back Today): Ends today in the future
     * 4. BLUE (Back Tomorrow): Ends tomorrow
     * 5. RED (Away Long-term): Ends day after tomorrow or later
     * 
     * @param \App\Models\User $user The employee
     * @param \App\Models\Movement|null $movement Their current active movement
     * @return array Associative array of styling properties
     */
    public function getCardData(User $user, ?Movement $movement): array
    {
        // Default state: PRESENT (Green)
        $data = [
            'statusType'  => 'present',
            'statusColor' => '#10b981', // emerald-500
            'borderColor' => '#059669', // emerald-600
            'badgeBg'     => '#064e3b', // emerald-900
            'badgeText'   => '#d1fae5', // emerald-100
            'badgeLabel'  => 'PRESENT',
            'typeLabel'   => 'In Office',
            'iconName'    => 'check-circle'
        ];

        // If no active movement, user is present
        if (!$movement) {
            return $data;
        }

        // Set movement type label and icon
        $data['typeLabel'] = Movement::TYPES[$movement->movement_type] 
            ?? strtoupper(str_replace('_', ' ', $movement->movement_type));
        
        $data['iconName'] = match ($movement->movement_type) {
            'meeting'  => 'users',
            'travel'   => 'plane',
            'leave'    => 'palmtree',
            'sick'     => 'heart-pulse',
            default    => 'map-pin',
        };

        $end = $movement->ended_at;

        // Determine status based on end date
        if (!$end) {
            // PURPLE: No end date set (indefinite absence)
            $data['statusType']  = 'away_indefinite';
            $data['statusColor'] = '#8b5cf6'; // violet-500
            $data['borderColor'] = '#7c3aed'; // violet-600
            $data['badgeBg']     = '#4c1d95'; // violet-900
            $data['badgeText']   = '#ede9fe'; // violet-100
            $data['badgeLabel']  = 'OUT';
        } elseif ($end->isToday()) {
            // YELLOW: Returns today (but end time is in the future)
            $data['statusType']  = 'back_today';
            $data['statusColor'] = '#f59e0b'; // amber-500
            $data['borderColor'] = '#d97706'; // amber-600
            $data['badgeBg']     = '#78350f'; // amber-900
            $data['badgeText']   = '#fef3c7'; // amber-100
            $data['badgeLabel']  = 'BACK TODAY';
        } elseif ($end->isTomorrow()) {
            // BLUE: Returns tomorrow
            $data['statusType']  = 'back_tomorrow';
            $data['statusColor'] = '#0ea5e9'; // sky-500
            $data['borderColor'] = '#0284c7'; // sky-600
            $data['badgeBg']     = '#082f49'; // sky-950
            $data['badgeText']   = '#e0f2fe'; // sky-100
            $data['badgeLabel']  = 'TOMORROW';
        } else {
            // RED: Returns day after tomorrow or later (long-term absence)
            $data['statusType']  = 'away_long';
            $data['statusColor'] = '#f43f5e'; // rose-500
            $data['borderColor'] = '#e11d48'; // rose-600
            $data['badgeBg']     = '#881337'; // rose-900
            $data['badgeText']   = '#ffe4e6'; // rose-100
            $data['badgeLabel']  = 'AWAY';
        }

        return $data;
    }

    /**
     * Render the presence board view.
     * 
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.public-board.presence-board')
            ->layout('components.layouts.public');
    }
}
