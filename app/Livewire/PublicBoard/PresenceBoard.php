<?php
// app/Livewire/PublicBoard/PresenceBoard.php

namespace App\Livewire\PublicBoard;

use App\Models\User;
use App\Models\Movement;
use Livewire\Component;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class PresenceBoard extends Component
{
    public int $page = 1;
    public int $perPage = 8; // Increased for card layout

    /**
     * Calculate total number of pages
     */
    #[Computed]
    public function totalPages(): int
    {
        $totalUsers = Cache::remember('presence_board_total_users', 30, function() {
            return User::employee()->count();
        });
        
        return max(1, (int) ceil($totalUsers / $this->perPage));
    }

    /**
     * Get paginated users with their current movements
     */
    #[Computed]
    public function users()
    {
        $cacheKey = "presence_board_page_{$this->page}";
        
        return Cache::remember($cacheKey, 8, function() {
            $users = User::employee()
                ->with(['movements' => function ($query) {
                    $now = now();
                    $query->whereIn('status', [Movement::STATUS_PLANNED, Movement::STATUS_ACTIVE])
                        ->where('started_at', '<=', $now)
                        ->where(fn($q) => $q->whereNull('ended_at')->orWhere('ended_at', '>=', $now))
                        ->orderBy('started_at', 'desc')
                        ->limit(1);
                }])
                ->orderBy('name')
                ->skip(($this->page - 1) * $this->perPage)
                ->take($this->perPage)
                ->get();

            // Add currentMovement accessor to each user
            $users->each(function($user) {
                $user->currentMovement = $user->movements->first();
            });

            return $users;
        });
    }

    /**
     * Get count of employees currently present
     */
    public function getPresentCountProperty(): int
    {
        return Cache::remember('presence_board_present_count', 30, function() {
            $allUsers = User::employee()->count();
            $awayUsers = $this->getAwayCountProperty();
            return $allUsers - $awayUsers;
        });
    }

    /**
     * Get count of employees currently away
     */
    public function getAwayCountProperty(): int
    {
        return Cache::remember('presence_board_away_count', 30, function() {
            $now = now();
            return User::employee()
                ->whereHas('movements', function($query) use ($now) {
                    $query->whereIn('status', [Movement::STATUS_PLANNED, Movement::STATUS_ACTIVE])
                        ->where('started_at', '<=', $now)
                        ->where(fn($q) => $q->whereNull('ended_at')->orWhere('ended_at', '>=', $now));
                })
                ->count();
        });
    }

    /**
     * Get total employee count
     */
    public function getTotalUsersProperty(): int
    {
        return Cache::remember('presence_board_total_users', 30, function() {
            return User::employee()->count();
        });
    }

    /**
     * Rotate to next page automatically
     */
    public function rotatePage(): void
    {
        try {
            $totalPages = $this->totalPages();
            $this->page = ($this->page % $totalPages) + 1;
            
            // Clear cache for the new page to ensure fresh data
            Cache::forget("presence_board_page_{$this->page}");
        } catch (\Exception $e) {
            Log::error('Presence Board rotation error: ' . $e->getMessage());
            $this->page = 1;
        }
    }

    /**
     * Get card display data for an employee
     * 
     * @param User $user
     * @param Movement|null $movement
     * @return array
     */
    public function getCardData($user, $movement): array
    {
        // Default: Employee is available/in office
        $data = [
            'statusIcon' => '✓',
            'typeLabel' => 'IN OFFICE',
            'statusColor' => '#10b981', // Emerald-500
            'borderColor' => '#10b981',
            'badgeBg' => '#064e3b', // Emerald-900
            'badgeText' => '#d1fae5', // Emerald-100
            'badgeIcon' => '🟢',
        ];

        // If no movement, employee is present
        if (!$movement) {
            return $data;
        }

        // Employee has an active movement (away from office)
        $movementType = $movement->movement_type;
        $end = $movement->ended_at;

        // Set movement label
        $data['typeLabel'] = Movement::TYPES[$movementType] ?? strtoupper(str_replace('_', ' ', $movementType));

        // Determine icon based on movement type
        $data['statusIcon'] = match($movementType) {
            'vacation' => '🏖️',
            'sick_leave' => '🏥',
            'meeting' => '📋',
            'remote_work' => '🏠',
            'business_trip' => '✈️',
            'training' => '📚',
            default => '⏸️',
        };

        // Color coding based on return time
        if ($end) {
            if ($end->isToday()) {
                // Returning today - Amber (yellow)
                $data['statusColor'] = '#f59e0b';
                $data['borderColor'] = '#f59e0b';
                $data['badgeBg'] = '#78350f';
                $data['badgeText'] = '#fef3c7';
                $data['badgeIcon'] = '🟡';
            } elseif ($end->isTomorrow()) {
                // Returning tomorrow - Sky (blue)
                $data['statusColor'] = '#0ea5e9';
                $data['borderColor'] = '#0ea5e9';
                $data['badgeBg'] = '#082f49';
                $data['badgeText'] = '#e0f2fe';
                $data['badgeIcon'] = '🔵';
            } else {
                // Returning later - Rose (red)
                $data['statusColor'] = '#f43f5e';
                $data['borderColor'] = '#f43f5e';
                $data['badgeBg'] = '#881337';
                $data['badgeText'] = '#ffe4e6';
                $data['badgeIcon'] = '🔴';
            }
        } else {
            // No return date (indefinite) - Violet
            $data['statusColor'] = '#8b5cf6';
            $data['borderColor'] = '#8b5cf6';
            $data['badgeBg'] = '#4c1d95';
            $data['badgeText'] = '#ede9fe';
            $data['badgeIcon'] = '🟣';
        }

        return $data;
    }

    /**
     * Render the component
     */
    public function render()
    {
        return view('livewire.public-board.presence-board')
            ->layout('components.layouts.public');
    }
}
