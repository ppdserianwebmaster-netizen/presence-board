<?php

namespace App\Livewire\User;

use App\Models\Movement;
use App\Enums\MovementType;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class EmployeeDashboard extends Component
{
    use WithPagination;

    // Form Fields
    public $type = '';
    public $started_at;
    public $ended_at;
    public $remark;

    public bool $showingModal = false;

    public function openMovementModal()
    {
        $this->reset(['type', 'started_at', 'ended_at', 'remark']);
        $this->started_at = now()->format('Y-m-d\TH:i');
        $this->showingModal = true;
    }

    public function submitMovement()
    {
        $this->validate([
            'type' => 'required',
            'started_at' => 'required|date',
            'ended_at' => 'nullable|date|after_or_equal:started_at',
            'remark' => 'nullable|string|max:255',
        ]);

        Auth::user()->movements()->create([
            'type' => $this->type,
            'started_at' => $this->started_at,
            'ended_at' => $this->ended_at,
            'remark' => $this->remark,
        ]);

        $this->showingModal = false;
        $this->dispatch('notify', message: 'Record added successfully.');
    }

    public function deleteMovement(Movement $movement)
    {
        if ($movement->user_id === Auth::id()) {
            $movement->delete();
            $this->dispatch('notify', message: 'Record removed.');
        }
    }

    public function render()
    {
        return view('livewire.user.employee-dashboard', [
            'history' => Auth::user()->movements()->latest('started_at')->paginate(10),
            'types' => MovementType::cases(),
        ]);
    }
}
