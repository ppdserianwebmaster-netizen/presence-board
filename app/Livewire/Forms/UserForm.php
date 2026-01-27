<?php

namespace App\Livewire\Forms;

use App\Models\User;
use Livewire\Form;
use Illuminate\Validation\Rule;
use App\Enums\UserRole;

class UserForm extends Form
{
    public ?User $user = null;

    public string $name = '';
    public string $email = '';
    public string $employee_id = '';
    public string $department = '';
    public string $position = '';
    
    // Default to a string value that matches your Enum case
    public string $role = 'employee'; 
    
    public string $password = '';

    public function set(User $user)
    {
        $this->user = $user;
        
        // Fill the form properties directly from the model
        $this->fill($user->only([
            'name', 'email', 'employee_id', 'department', 'position'
        ]));
        
        // Enums cast automatically to strings when assigned to a string property
        $this->role = $user->role->value;
    }

    protected function rules(): array
    {
        return [
            'name' => 'required|min:3',
            'email' => [
                'required', 
                'email', 
                Rule::unique('users', 'email')->ignore($this->user?->id)
            ],
            'employee_id' => [
                'required', 
                Rule::unique('users', 'employee_id')->ignore($this->user?->id)
            ],
            'department' => 'required',
            'position' => 'required',
            'role' => ['required', Rule::enum(UserRole::class)],
            'password' => $this->user ? 'nullable|min:8' : 'required|min:8',
        ];
    }

    public function store()
    {
        $validated = $this->validate();

        User::create($validated);
        
        $this->reset();
    }

    public function update()
    {
        $validated = $this->validate();

        // Remove password if empty (user doesn't want to change it)
        if (empty($this->password)) {
            unset($validated['password']);
        }

        $this->user->update($validated);
        
        $this->reset();
    }
}
