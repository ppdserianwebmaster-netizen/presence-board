<?php

namespace App\Livewire\Public;

use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\On; // <-- NEW: Import the attribute

class PresenceBoard extends Component
{
    public int $page = 1;
    public int $perPage = 10;  // records per screen/page
    public int $totalPages = 1;

    public function mount(): void
    {
        // Only employees, not admins
        $totalUsers = User::employee()->count();
        $this->totalPages = max(1, (int) ceil($totalUsers / $this->perPage));
    }

    #[On('poll')] // <-- NEW: Automatically calls this method when the view polls
    public function rotatePage(): void
    {
        $this->page = ($this->page % $this->totalPages) + 1;
    }

    public function render()
    {
        // Pre-load the current_movement relationship efficiently
        $users = User::employee()
            ->orderBy('name')
            // Using with() to eager load the relationship will prevent the N+1 problem
            ->with(['movements' => function ($query) {
                // Eager load only the relevant movement using the same logic as the accessor
                $query->whereIn('status', [\App\Models\Movement::STATUS_PLANNED, \App\Models\Movement::STATUS_ACTIVE])
                    ->where('start_datetime', '<=', now())
                    ->where(function ($q) {
                        $q->whereNull('end_datetime')->orWhere('end_datetime', '>=', now());
                    });
            }])
            ->forPage($this->page, $this->perPage)
            ->get();
        
        // Map users to check if the accessor found a movement (since we eager loaded the collection)
        $users->each(function($user) {
            // The accessor currentMovement() will automatically find the eager-loaded relationship
            $user->is_available = is_null($user->current_movement); 
        });

        return view('livewire.public.presence-board', [
            'users' => $users,
        ])->layout('components.layouts.public');
    }
}
