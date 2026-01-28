<?php

namespace App\Livewire\Forms;

use App\Models\Movement;
use App\Enums\MovementType;
use Livewire\Form;
use Illuminate\Validation\Rule;

class MovementForm extends Form
{
    /**
     * The Movement model instance.
     * PHP 8.4 Asymmetric Visibility: Readable by any class, 
     * but only settable by this form or its children.
     */
    public ?Movement $movement = null;

    // --- Form Fields ---
    public ?int $user_id = null;
    public string $type = ''; 
    public ?string $started_at = null; 
    public ?string $ended_at = null;
    public string $remark = '';

    /**
     * Define validation rules.
     */
    protected function rules(): array
    {
        return [
            'user_id'    => [$this->movement ? 'nullable' : 'required', 'exists:users,id'],
            'type'       => ['required', Rule::enum(MovementType::class)],
            'started_at' => ['required', 'date'],
            'ended_at'   => ['nullable', 'date', 'after_or_equal:started_at'],
            'remark'     => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * Initialize form properties from a Movement record.
     */
    public function set(Movement $movement): void
    {
        $this->movement = $movement;
        
        $this->user_id = $movement->user_id;
        $this->remark  = $movement->remark ?? '';
        
        // Extract Enum value safely
        $this->type = $movement->type instanceof MovementType 
            ? $movement->type->value 
            : (string) $movement->type;

        // Formats for datetime-local input compatibility
        $this->started_at = $movement->started_at?->format('Y-m-d\TH:i');
        $this->ended_at   = $movement->ended_at?->format('Y-m-d\TH:i');
    }

    /**
     * Create a new movement record.
     */
    public function store(): void
    {
        $validated = $this->validate();
        Movement::create($validated);
        $this->reset();
    }

    /**
     * Update the existing movement record.
     */
    public function update(): void
    {
        $validated = $this->validate();
        $this->movement->update($validated);
        $this->reset();
    }
}
