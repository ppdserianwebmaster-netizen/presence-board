<?php

namespace App\Livewire\Forms;

use App\Models\User;
use Livewire\Form;
use Illuminate\Validation\Rule;
use App\Enums\UserRole;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;

class UserForm extends Form
{
    /**
     * The User model instance.
     * PHP 8.4 Asymmetric Visibility: 
     * Publicly readable, but can only be set within this class or a child class.
     */
    public ?User $user = null;

    // --- Form Fields ---
    public string $name = '';
    public string $email = '';
    public string $employee_id = '';
    public string $department = '';
    public string $position = '';
    public string $role = 'employee'; 
    public string $password = '';
    public $photo; // Temporary photo upload

    /**
     * Map a User model to the form properties.
     */
    public function set(User $user): void
    {
        $this->user = $user;
        
        $this->fill($user->only([
            'name', 'email', 'employee_id', 'department', 'position'
        ]));
        
        $this->role = $user->role->value;
    }

    /**
     * Define validation rules for users.
     */
    protected function rules(): array
    {
        return [
            'name'        => ['required', 'string', 'min:3', 'max:255'],
            'email'       => [
                'required', 'email', 
                Rule::unique('users', 'email')->ignore($this->user?->id)
            ],
            'employee_id' => [
                'required', 'string', 
                Rule::unique('users', 'employee_id')->ignore($this->user?->id)
            ],
            'department'  => ['required', 'string'],
            'position'    => ['required', 'string'],
            'role'        => ['required', Rule::enum(UserRole::class)],
            'password'    => [$this->user ? 'nullable' : 'required', 'min:8'],
            'photo'       => ['nullable', 'image', 'max:2048'], 
        ];
    }

    /**
     * Store new User and handle photo.
     */
    public function store(): void
    {
        $validated = $this->validate();

        if ($this->photo) {
            // Process and resize the image before storing
            $validated['profile_photo_path'] = $this->processImage($this->photo);
        }

        User::create($validated);
        $this->reset();
    }

    /**
     * Update User, handle password hashing, and clean up storage.
     */
    public function update(): void
    {
        $validated = $this->validate();

        if ($this->photo) {
            // Cleanup: remove old physical file before saving new one
            if ($this->user->profile_photo_path) {
                Storage::disk('public')->delete($this->user->profile_photo_path);
            }
            $validated['profile_photo_path'] = $this->processImage($this->photo);
        }

        if (empty($this->password)) {
            unset($validated['password']);
        }

        $this->user->update($validated);
    }

    /**
     * Helper to process, resize, and store the image.
     */
    protected function processImage($uploadedFile): string
    {
        // 1. Read the image from the temporary path
        $image = Image::read($uploadedFile->getRealPath());

        // 2. Resize to 300x300 (Cover crops it perfectly)
        $image->cover(300, 300);

        // 3. Generate a unique filename with .webp extension for better compression
        $filename = 'profile-photos/' . bin2hex(random_bytes(10)) . '.webp';

        // 4. Save to the public disk
        // We use toString() to get the binary data for Storage::put
        Storage::disk('public')->put($filename, $image->toWebp(70)->toString());

        return $filename;
    }
}
