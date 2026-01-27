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
    public string $type = ''; // Type-hinted as string for the HTML select binding
    public $started_at;
    public $ended_at;
    public string $remark = '';

    /**
     * Common validation rules used by both store and update.
     */
    protected function rules(): array
    {
        return [
            // user_id is only required on create (since it's hidden/locked on edit)
            'user_id' => [$this->movement ? 'nullable' : 'required', 'exists:users,id'],
            'type' => ['required', Rule::enum(MovementType::class)],
            'started_at' => 'required|date',
            'ended_at' => 'nullable|date|after_or_equal:started_at',
            'remark' => 'nullable|string|max:500',
        ];
    }

    public function set(Movement $movement)
    {
        $this->movement = $movement;
        
        // Use fill for basic attributes
        $this->fill($movement->only(['user_id', 'remark']));

        // Ensure we extract the value from the Enum for the select input
        $this->type = $movement->type instanceof MovementType 
            ? $movement->type->value 
            : (string) $movement->type;

        // Formats for datetime-local input compatibility
        $this->started_at = $movement->started_at?->format('Y-m-d\TH:i');
        $this->ended_at   = $movement->ended_at?->format('Y-m-d\TH:i');
    }

    public function store()
    {
        $validated = $this->validate();

        Movement::create($validated);
        
        $this->reset();
    }

    public function update()
    {
        // Validation automatically ignores user_id logic if $this->movement exists
        $validated = $this->validate();

        $this->movement->update($validated);
        
        $this->reset();
    }
}
