<?php

namespace App\Livewire\User;

use App\Models\Movement;
use App\Enums\MovementType;
use App\Livewire\Forms\MovementForm;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\View\View;

/**
 * EmployeeDashboard Component
 * Allows employees to track their own out-of-office movements.
 */
class EmployeeDashboard extends Component
{
    use WithPagination, AuthorizesRequests;

    /**
     * Reusable Form Object for Movement logic.
     */
    public MovementForm $form;

    /**
     * Search term for filtering history.
     */
    public string $search = '';

    /**
     * UI State for the modal.
     */
    public bool $showingModal = false;

    /**
     * Reset pagination when searching.
     */
    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Prepare the form for a new entry.
     */
    public function openMovementModal(): void
    {
        $this->form->reset();
        
        // Contextual defaults for the current user
        $this->form->user_id = Auth::id();
        $this->form->started_at = now()->format('Y-m-d\TH:i');
        
        $this->showingModal = true;
    }

    /**
     * Execute movement log submission.
     */
    public function submitMovement(): void
    {
        // Enforce ownership: ensure user_id is always the logged-in user
        $this->form->user_id = Auth::id();
        
        $this->form->store();

        $this->showingModal = false;
        $this->dispatch('notify', message: 'Movement record added successfully.');
    }

    /**
     * Remove a movement record with strict ownership check.
     */
    public function deleteMovement(Movement $movement): void
    {
        // Security: Prevent users from deleting others' records via ID tampering
        if ($movement->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $movement->delete();
        $this->dispatch('notify', message: 'Record removed.');
    }

    /**
     * Render the employee personal movement view.
     */
    public function render(): View
    {
        $query = Auth::user()->movements();

        // Optional Search Filtering
        if ($this->search) {
            $query->where('remark', 'like', '%' . $this->search . '%');
        }

        return view('livewire.user.employee-dashboard', [
            'history' => $query->latest('started_at')->paginate(10),
            'types' => MovementType::cases(),
        ])->layout('components.layouts.app');
    }
}
