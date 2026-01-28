<?php

namespace App\Livewire\Admin\Movement;

use App\Models\User;
use App\Models\Movement;
use App\Livewire\Forms\MovementForm;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

class MovementIndex extends Component
{
    use WithPagination;

    public MovementForm $form;
    
    // UI State
    public string $search = '';
    public bool $showingModal = false;

    /**
     * Reset pagination when search query changes
     */
    public function updatedSearch(): void 
    { 
        $this->resetPage(); 
    }

    /**
     * Prepare form for a new movement record
     */
    public function create(): void
    {
        $this->form->reset();
        
        // Initialize default timestamp for the datetime-local input
        $this->form->started_at = now()->format('Y-m-d\TH:i');
        
        $this->showingModal = true;
    }

    /**
     * Load existing movement into the form object
     */
    public function edit(Movement $movement): void
    {
        $this->form->set($movement);
        $this->showingModal = true;
    }

    /**
     * Handle store or update via the Form Object
     */
    public function save(): void
    {
        // Validation and Execution handled by UserForm object
        $this->form->movement ? $this->form->update() : $this->form->store();
        
        $this->showingModal = false;
        
        $this->dispatch('notify', message: 'Movement record saved successfully.');
    }

    /**
     * Remove a movement record
     */
    public function delete(Movement $movement): void
    {
        $movement->delete();
        $this->dispatch('notify', message: 'Movement record deleted.');
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
        return view('livewire.admin.movement.movement-index', [
            'movements' => Movement::query()
                // 1. Eager load user including those that are soft-deleted
                ->with(['user' => fn($q) => $q->withTrashed()]) 
                ->when($this->search, function($query) {
                    // 2. Allow searching through deleted users too
                    $query->whereHas('user', function($q) {
                        $q->withTrashed() 
                        ->where(function($inner) {
                            $inner->where('name', 'like', "%{$this->search}%")
                                    ->orWhere('employee_id', 'like', "%{$this->search}%");
                        });
                    });
                })
                ->latest('started_at')
                ->paginate(15),
            
            // Droplist should stay clean (only active users for new logs)
            'users' => User::orderBy('name')->select('id', 'name', 'employee_id')->get(),
        ]);
    }
}
