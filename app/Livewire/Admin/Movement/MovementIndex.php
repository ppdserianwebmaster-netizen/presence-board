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
    public string $search = '';
    public bool $showingModal = false;

    public function updatedSearch() { $this->resetPage(); }

    public function create()
    {
        $this->form->reset();
        $this->form->started_at = now()->format('Y-m-d\TH:i');
        $this->showingModal = true;
    }

    public function edit(Movement $movement)
    {
        $this->form->set($movement);
        $this->showingModal = true;
    }

    public function save()
    {
        $this->form->movement ? $this->form->update() : $this->form->store();
        $this->showingModal = false;
        $this->dispatch('notify', message: 'Movement record saved successfully.');
    }

    public function delete(Movement $movement)
    {
        $movement->delete();
        $this->dispatch('notify', message: 'Movement record deleted.');
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
        return view('livewire.admin.movement.movement-index', [
            'movements' => Movement::with('user')
                ->whereHas('user', function($q) {
                    $q->where('name', 'like', "%{$this->search}%");
                })
                ->latest('started_at')
                ->paginate(15),
            'users' => User::orderBy('name')->get(),
        ]);
    }
}
