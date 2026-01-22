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
    
    // Change string to UserRole|string
    public UserRole|string $role = 'employee'; 
    
    public string $password = '';

    public function set(User $user)
    {
        $this->user = $user;
        
        // Manually assign or ensure value is extracted
        $this->name = $user->name;
        $this->email = $user->email;
        $this->employee_id = $user->employee_id;
        $this->department = $user->department;
        $this->position = $user->position;
        
        // Extract the string value from the Enum
        $this->role = $user->role instanceof UserRole ? $user->role->value : $user->role;
    }

    public function store()
    {
        $validated = $this->validate([
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users,email',
            'employee_id' => 'required|unique:users,employee_id',
            'department' => 'required',
            'position' => 'required',
            'role' => ['required', Rule::enum(UserRole::class)],
            'password' => 'required|min:8',
        ]);

        User::create($validated);
        $this->reset();
    }

    public function update()
    {
        $rules = [
            'name' => 'required|min:3',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($this->user->id)],
            'employee_id' => ['required', Rule::unique('users', 'employee_id')->ignore($this->user->id)],
            'department' => 'required',
            'position' => 'required',
            'role' => ['required', Rule::enum(UserRole::class)],
            'password' => 'nullable|min:8',
        ];

        $validated = $this->validate($rules);
        if (empty($validated['password'])) unset($validated['password']);

        $this->user->update($validated);
        $this->reset();
    }
}
