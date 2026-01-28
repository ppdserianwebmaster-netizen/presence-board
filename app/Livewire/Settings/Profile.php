<?php

namespace App\Livewire\Settings;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads; // Add this for photo uploads
use Livewire\Attributes\Layout;

class Profile extends Component
{
    // #[Layout('components.layouts.app')]
    // public string $name = '';

    // public string $email = '';

    // /**
    //  * Mount the component.
    //  */
    // public function mount(): void
    // {
    //     $this->name = Auth::user()->name;
    //     $this->email = Auth::user()->email;
    // }

    // /**
    //  * Update the profile information for the currently authenticated user.
    //  */
    // public function updateProfileInformation(): void
    // {
    //     $user = Auth::user();

    //     $validated = $this->validate([
    //         'name' => ['required', 'string', 'max:255'],

    //         'email' => [
    //             'required',
    //             'string',
    //             'lowercase',
    //             'email',
    //             'max:255',
    //             Rule::unique(User::class)->ignore($user->id),
    //         ],
    //     ]);

    //     $user->fill($validated);

    //     if ($user->isDirty('email')) {
    //         $user->email_verified_at = null;
    //     }

    //     $user->save();

    //     $this->dispatch('profile-updated', name: $user->name);
    // }

    use WithFileUploads;

    #[Layout('components.layouts.app')]
    public string $name = '';
    public string $email = '';
    public string $employee_id = '';
    public string $department = '';
    public string $position = '';
    public $photo; // For new uploads

    public function mount(): void
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->email = $user->email;
        $this->employee_id = $user->employee_id ?? '';
        $this->department = $user->department ?? '';
        $this->position = $user->position ?? '';
    }

    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
            'employee_id' => ['nullable', 'string', 'max:50'],
            'department' => ['nullable', 'string', 'max:100'],
            'position' => ['nullable', 'string', 'max:100'],
            'photo' => ['nullable', 'image', 'max:1024'], // 1MB Max
        ]);

        if ($this->photo) {
            $validated['profile_photo_path'] = $this->photo->store('profile-photos', 'public');
        }

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $this->dispatch('profile-updated', name: $user->name);
    }

    /**
     * Send an email verification notification to the current user.
     */
    public function resendVerificationNotification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));

            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }
}
