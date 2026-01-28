<?php

namespace App\Livewire\Admin\User;

use App\Models\User;
use App\Livewire\Forms\UserForm;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;

#[Layout('components.layouts.app')]
#[Title('User Management')]
class UserIndex extends Component
{
    use WithPagination, WithFileUploads;

    /**
     * The Form Object handling all validation and persistence logic.
     */
    public UserForm $form;
    
    /** @var string The current search query for filtering users. */
    public string $search = '';
    
    /** @var bool Toggle state for the creation/editing modal. */
    public bool $showingModal = false;

    /**
     * Reset pagination when search query changes. 
     * Ensures you aren't stuck on an empty page 2 if results only fill page 1.
     */
    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Prepare the form for a fresh user registration.
     */
    public function create(): void
    {
        $this->form->reset();
        // Since we reverted asymmetric visibility for Livewire compatibility, 
        // we can still manually ensure the user model is null.
        $this->form->user = null;
        
        $this->showingModal = true;
    }

    /**
     * Hydrate the Form Object with an existing user's data for editing.
     */
    public function edit(User $user): void
    {
        $this->form->set($user);
        $this->showingModal = true;
    }

    /**
     * Handles both the storage of new users and updating of existing ones.
     */
    public function save(): void
    {
        // Simple ternary: if form->user exists, we are editing; otherwise, creating.
        $this->form->user ? $this->form->update() : $this->form->store();
        
        // Reset the temporary photo property to clear memory and preview states.
        $this->form->photo = null; 
        
        $this->showingModal = false;
        
        $this->dispatch('notify', 
            message: 'User account processed successfully.', 
            type: 'success'
        );
    }

    /**
     * Soft delete a user using the 'SoftDeletes' trait on the model.
     */
    public function delete(User $user): void 
    { 
        if ($user->is(Auth::user())) {
            $this->dispatch('notify', message: 'You cannot archive your own account.', type: 'error');
            return;
        }

        $user->delete(); 
        $this->dispatch('notify', message: 'User archived.', type: 'info');
    }

    /**
     * Restore a soft-deleted user.
     */
    public function restore(int|string $id): void 
    { 
        $user = User::withTrashed()->findOrFail($id);
        $user->restore(); 
        
        $this->dispatch('notify', message: 'User access restored.', type: 'success');
    }

    /**
     * Permanently remove a user record from the database.
     */
    public function forceDelete(int|string $id): void 
    { 
        if ((int) $id === Auth::id()) {
            $this->dispatch('notify', message: 'Self-deletion is blocked.', type: 'error');
            return;
        }
        
        User::withTrashed()->findOrFail($id)->forceDelete(); 
        $this->dispatch('notify', message: 'User permanently removed.', type: 'warning');
    }

    /**
     * Render the view with paginated and filtered results.
     */
    public function render()
    {
        return view('livewire.admin.user.user-index', [
            'users' => User::withTrashed()
                ->when($this->search, function ($query) {
                    $query->where(fn($q) => 
                        $q->where('name', 'like', "%{$this->search}%")
                          ->orWhere('email', 'like', "%{$this->search}%")
                          ->orWhere('employee_id', 'like', "%{$this->search}%")
                    );
                })
                ->orderBy('name')
                ->paginate(10),
        ]);
    }
}
