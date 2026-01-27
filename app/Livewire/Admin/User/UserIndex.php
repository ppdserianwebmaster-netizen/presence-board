<?php

namespace App\Livewire\Admin\User;

use App\Models\User;
use App\Livewire\Forms\UserForm;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('components.layouts.app')]
#[Title('User Management')]
class UserIndex extends Component
{
    use WithPagination;

    public UserForm $form;
    
    public string $search = '';
    
    public bool $showingModal = false;

    /**
     * Reset pagination when search query changes.
     */
    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function create(): void
    {
        $this->form->reset();
        $this->form->user = null;
        $this->showingModal = true;
    }

    public function edit(User $user): void
    {
        $this->form->set($user);
        $this->showingModal = true;
    }

    public function save(): void
    {
        // PHP 8.4: Improved readability with ternary or match if logic grows
        $this->form->user ? $this->form->update() : $this->form->store();
        
        $this->showingModal = false;
        
        $this->dispatch('notify', 
            message: 'User account processed successfully.', 
            type: 'success'
        );
    }

    /**
     * Soft delete a user.
     */
    public function delete(User $user): void 
    { 
        if ($user->is(auth()->user())) {
            $this->dispatch('notify', message: 'You cannot archive your own account.', type: 'error');
            return;
        }

        $user->delete(); 
        $this->dispatch('notify', message: 'User archived.');
    }

    /**
     * Restore a soft-deleted user.
     */
    public function restore(int|string $id): void 
    { 
        $user = User::withTrashed()->findOrFail($id);
        $user->restore(); 
        
        $this->dispatch('notify', message: 'User access restored.');
    }

    /**
     * Permanently remove a user from the database.
     */
    public function forceDelete(int|string $id): void 
    { 
        if ((int) $id === auth()->id()) {
            return;
        }
        
        User::withTrashed()->findOrFail($id)->forceDelete(); 
        $this->dispatch('notify', message: 'User permanently removed.');
    }

    public function render()
    {
        return view('livewire.admin.user.user-index', [
            'users' => User::withTrashed()
                ->when($this->search, fn($q) => $q->where(function($query) {
                    $query->where('name', 'like', "%{$this->search}%")
                          ->orWhere('email', 'like', "%{$this->search}%")
                          ->orWhere('employee_id', 'like', "%{$this->search}%");
                }))
                ->orderBy('name')
                ->paginate(10),
        ]);
    }
}
