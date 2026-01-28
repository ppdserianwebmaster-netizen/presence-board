{{-- <section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Profile')" :subheading="__('Update your name and email address')">
        <form wire:submit="updateProfileInformation" class="my-6 w-full space-y-6">
            <flux:input wire:model="name" :label="__('Name')" type="text" required autofocus autocomplete="name" />

            <div>
                <flux:input wire:model="email" :label="__('Email')" type="email" required autocomplete="email" />

                @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail &&! auth()->user()->hasVerifiedEmail())
                    <div>
                        <flux:text class="mt-4">
                            {{ __('Your email address is unverified.') }}

                            <flux:link class="text-sm cursor-pointer" wire:click.prevent="resendVerificationNotification">
                                {{ __('Click here to re-send the verification email.') }}
                            </flux:link>
                        </flux:text>

                        @if (session('status') === 'verification-link-sent')
                            <flux:text class="mt-2 font-medium !dark:text-green-400 !text-green-600">
                                {{ __('A new verification link has been sent to your email address.') }}
                            </flux:text>
                        @endif
                    </div>
                @endif
            </div>

            <div class="flex items-center gap-4">
                <div class="flex items-center justify-end">
                    <flux:button variant="primary" type="submit" class="w-full">{{ __('Save') }}</flux:button>
                </div>

                <x-action-message class="me-3" on="profile-updated">
                    {{ __('Saved.') }}
                </x-action-message>
            </div>
        </form>

        <livewire:settings.delete-user-form />
    </x-settings.layout>
</section> --}}

<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Profile Information')" :subheading="__('Update your personal and employment details.')">
        <form wire:submit="updateProfileInformation" class="my-6 w-full space-y-8">
            
            {{-- Photo Section --}}
            <div class="flex items-center gap-6 p-4 bg-neutral-50 dark:bg-neutral-900/50 rounded-2xl border border-neutral-100 dark:border-neutral-800">
                <div class="relative group">
                    <img src="{{ $photo ? $photo->temporaryUrl() : auth()->user()->profile_photo_url }}" 
                         class="h-20 w-20 rounded-2xl object-cover border-2 border-white dark:border-neutral-800 shadow-sm">
                    
                    <label class="absolute inset-0 flex items-center justify-center bg-black/40 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer">
                        <flux:icon.camera class="text-white size-6" />
                        <input type="file" wire:model="photo" class="hidden">
                    </label>
                </div>
                <div>
                    <flux:heading size="sm">{{ __('Profile Photo') }}</flux:heading>
                    <flux:text size="sm">{{ __('Click the image to upload a new avatar.') }}</flux:text>
                    <div wire:loading wire:target="photo" class="text-[10px] font-bold text-indigo-500 uppercase mt-1">Uploading...</div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Personal Info --}}
                <flux:input wire:model="name" :label="__('Full Name')" icon="user" required />
                <flux:input wire:model="email" :label="__('Email Address')" icon="envelope" type="email" required />

                {{-- Employment Info --}}
                <flux:input wire:model="employee_id" :label="__('Employee ID')" icon="identification" placeholder="e.g. EMP-001" />
                <flux:input wire:model="position" :label="__('Job Position')" icon="briefcase" placeholder="e.g. Senior Developer" />
                
                <div class="md:col-span-2">
                    <flux:input wire:model="department" :label="__('Department')" icon="building-office" placeholder="e.g. Information Technology" />
                </div>
            </div>

            {{-- Email Verification Warning (unchanged logic) --}}
            @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! auth()->user()->hasVerifiedEmail())
                <div class="p-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-100 dark:border-amber-900/30 rounded-xl">
                    <flux:text class="text-amber-700 dark:text-amber-400">
                        {{ __('Your email address is unverified.') }}
                        <flux:link class="ml-2 font-bold cursor-pointer" wire:click.prevent="resendVerificationNotification">
                            {{ __('Resend Verification') }}
                        </flux:link>
                    </flux:text>
                </div>
            @endif

            <div class="flex items-center gap-4 pt-4 border-t border-neutral-100 dark:border-neutral-800">
                <flux:button variant="primary" type="submit" icon="check">{{ __('Save Changes') }}</flux:button>

                <x-action-message on="profile-updated">
                    <span class="text-sm font-bold text-green-600 dark:text-green-400">{{ __('Profile Updated!') }}</span>
                </x-action-message>
            </div>
        </form>

        <div class="mt-12 pt-12 border-t border-neutral-100 dark:border-neutral-800">
            <livewire:settings.delete-user-form />
        </div>
    </x-settings.layout>
</section>
