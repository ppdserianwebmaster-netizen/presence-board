<?php

namespace App\Livewire\Admin\User;

use App\Models\User;
use App\Livewire\Forms\UserForm;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

class UserIndex extends Component
{
    use WithPagination;

    public UserForm $form;
    public string $search = '';
    public bool $showingModal = false;

    public function updatedSearch() { $this->resetPage(); }

    public function create()
    {
        $this->form->reset();
        $this->form->user = null;
        $this->showingModal = true;
    }

    public function edit(User $user)
    {
        $this->form->set($user);
        $this->showingModal = true;
    }

    public function save()
    {
        $this->form->user ? $this->form->update() : $this->form->store();
        $this->showingModal = false;
        $this->dispatch('notify', message: 'Success!');
    }

    public function delete(User $user) 
    { 
        // Prevent archiving yourself
        if ($user->id === auth()->id()) {
            // Optional: add a session flash or notification here
            return;
        }
        $user->delete(); 
    }

    public function restore($id) 
    { 
        // Logic: You can only restore others, or if you were soft-deleted 
        // (though usually, you can't log in if soft-deleted)
        User::withTrashed()->find($id)->restore(); 
    }

    public function forceDelete($id) 
    { 
        // Prevent permanently wiping yourself from the database
        if ($id == auth()->id()) {
            return;
        }
        
        User::withTrashed()->find($id)->forceDelete(); 
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
        return view('livewire.admin.user.user-index', [
            'users' => User::withTrashed()
                ->where('name', 'like', "%{$this->search}%")
                ->orderBy('name')
                ->paginate(10),
        ]);
    }
}
