<?php

namespace App\Livewire\Forms;

use App\Models\Movement;
use App\Enums\MovementType;
use Livewire\Form;
use Illuminate\Validation\Rule;

class MovementForm extends Form
{
    public ?Movement $movement = null;

    public $user_id;
    public $type = '';
    public $started_at;
    public $ended_at;
    public $remark = '';

    public function set(Movement $movement)
    {
        $this->movement   = $movement;
        $this->user_id    = $movement->user_id;
        $this->type       = $movement->type->value;
        $this->remark     = $movement->remark;

        // PHP 8.4 style formatting
        $this->started_at = $movement->started_at?->format('Y-m-d\TH:i');
        $this->ended_at   = $movement->ended_at?->format('Y-m-d\TH:i');
    }

    public function store()
    {
        $validated = $this->validate([
            'user_id' => 'required|exists:users,id',
            'type' => ['required', Rule::enum(MovementType::class)],
            'started_at' => 'required|date',
            'ended_at' => 'nullable|date|after_or_equal:started_at',
            'remark' => 'nullable|string|max:500',
        ]);

        Movement::create($validated);
        $this->reset();
    }

    public function update()
    {
        $validated = $this->validate([
            'type' => ['required', Rule::enum(MovementType::class)],
            'started_at' => 'required|date',
            'ended_at' => 'nullable|date|after_or_equal:started_at',
            'remark' => 'nullable|string|max:500',
        ]);

        $this->movement->update($validated);
        $this->reset();
    }
}
