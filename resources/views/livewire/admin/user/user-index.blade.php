{{-- resources/views/livewire/admin/user/user-index.blade.php --}}
<div class="min-h-screen bg-gradient-to-br from-neutral-50 via-white to-neutral-50 dark:from-neutral-950 dark:via-neutral-900 dark:to-neutral-950 p-6">
    <div class="max-w-7xl mx-auto">
        <div class="flex flex-col gap-8">
            {{-- Header Section with Enhanced Design --}}
            <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6">
                <div class="space-y-2">
                    <div class="flex items-center gap-3">
                        <div class="w-1.5 h-8 bg-gradient-to-b from-neutral-900 to-neutral-600 dark:from-white dark:to-neutral-400 rounded-full"></div>
                        <h1 class="text-3xl font-black dark:text-white tracking-tight bg-gradient-to-r from-neutral-900 to-neutral-600 dark:from-white dark:to-neutral-300 bg-clip-text text-transparent">
                            Personnel
                        </h1>
                    </div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400 font-semibold ml-5">
                        Manage account access and roles
                    </p>
                </div>
                <button wire:click="create" 
                    class="group px-6 py-3 text-[10px] font-black uppercase tracking-widest bg-gradient-to-br from-neutral-900 to-neutral-700 dark:from-white dark:to-neutral-200 text-white dark:text-black rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105 active:scale-95">
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4 transition-transform group-hover:rotate-90 duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                        </svg>
                        Add User
                    </span>
                </button>
            </div>

            {{-- Search Bar with Enhanced Styling --}}
            <div class="relative max-w-md">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-neutral-400 dark:text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search personnel..." 
                    class="w-full pl-12 pr-4 py-3.5 bg-white dark:bg-neutral-900 border-2 border-neutral-200 dark:border-neutral-800 focus:border-neutral-400 dark:focus:border-neutral-600 focus:ring-4 focus:ring-neutral-100 dark:focus:ring-neutral-800/50 transition-all duration-300 rounded-2xl text-sm dark:text-white outline-none font-medium placeholder:text-neutral-400 shadow-sm focus:shadow-md">
            </div>

            {{-- Table Container with Premium Design --}}
            <div class="overflow-hidden rounded-3xl border-2 border-neutral-100 dark:border-neutral-800 bg-white dark:bg-neutral-900 shadow-xl">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gradient-to-r from-neutral-50/80 via-white to-neutral-50/80 dark:from-neutral-800/50 dark:via-neutral-900 dark:to-neutral-800/50 text-neutral-600 dark:text-neutral-400 uppercase text-[9px] font-black tracking-[0.2em] border-b-2 border-neutral-100 dark:border-neutral-800">
                            <tr>
                                <th class="px-8 py-5 text-left">
                                    <span class="flex items-center gap-2">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        Employee
                                    </span>
                                </th>
                                <th class="px-8 py-5 text-left">
                                    <span class="flex items-center gap-2">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Status
                                    </span>
                                </th>
                                <th class="px-8 py-5 text-left">
                                    <span class="flex items-center gap-2">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                        </svg>
                                        System Role
                                    </span>
                                </th>
                                <th class="px-8 py-5 text-right">
                                    <span class="flex items-center justify-end gap-2">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                                        </svg>
                                        Actions
                                    </span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y-2 divide-neutral-50 dark:divide-neutral-800/50">
                            @foreach ($users as $user)
                                <tr wire:key="{{ $user->id }}" class="group hover:bg-gradient-to-r hover:from-neutral-50/50 hover:via-transparent hover:to-neutral-50/50 dark:hover:from-neutral-800/30 dark:hover:via-transparent dark:hover:to-neutral-800/30 transition-all duration-300 {{ $user->trashed() ? 'opacity-50' : '' }}">
                                    <td class="px-8 py-5">
                                        <div class="flex items-center gap-4">
                                            {{-- Enhanced Avatar --}}
                                            <div class="relative group-hover:scale-110 transition-transform duration-300">
                                                <div class="h-12 w-12 rounded-2xl bg-gradient-to-br from-neutral-100 to-neutral-200 dark:from-neutral-800 dark:to-neutral-700 text-neutral-600 dark:text-neutral-300 flex items-center justify-center font-black text-sm border-2 border-neutral-200 dark:border-neutral-700 overflow-hidden shadow-sm group-hover:shadow-md transition-all duration-300">
                                                    @if($user->profile_photo_path)
                                                        <img src="{{ $user->profile_photo_url }}" class="h-full w-full object-cover">
                                                    @else
                                                        {{ $user->initials() }}
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="space-y-1">
                                                <div class="font-black text-neutral-900 dark:text-white group-hover:text-neutral-700 dark:group-hover:text-neutral-200 transition-colors duration-300">
                                                    {{ $user->name }}
                                                </div>
                                                <div class="text-[9px] text-neutral-400 dark:text-neutral-500 font-black tracking-wider uppercase flex items-center gap-2">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                                                    </svg>
                                                    {{ $user->employee_id }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-5">
                                        @if($user->trashed())
                                            <span class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-[9px] font-black uppercase tracking-wider bg-gradient-to-r from-rose-100 to-rose-50 text-rose-700 dark:from-rose-900/40 dark:to-rose-900/20 dark:text-rose-400 border-2 border-rose-200 dark:border-rose-800 shadow-sm">
                                                <div class="w-2 h-2 rounded-full bg-rose-500"></div>
                                                Archived
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-[9px] font-black uppercase tracking-wider bg-gradient-to-r from-emerald-100 to-emerald-50 text-emerald-700 dark:from-emerald-900/40 dark:to-emerald-900/20 dark:text-emerald-400 border-2 border-emerald-200 dark:border-emerald-800 shadow-sm">
                                                <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse shadow-sm shadow-emerald-500/50"></div>
                                                Active
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-8 py-5">
                                        <span class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-[9px] font-black uppercase tracking-wider 
                                            {{ $user->role->value === 'admin' 
                                                ? 'bg-gradient-to-r from-purple-100 to-purple-50 text-purple-700 dark:from-purple-900/40 dark:to-purple-900/20 dark:text-purple-400 border-2 border-purple-200 dark:border-purple-800' 
                                                : 'bg-gradient-to-r from-blue-100 to-blue-50 text-blue-700 dark:from-blue-900/40 dark:to-blue-900/20 dark:text-blue-400 border-2 border-blue-200 dark:border-blue-800' }} shadow-sm">
                                            @if($user->role->value === 'admin')
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                                </svg>
                                            @else
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                </svg>
                                            @endif
                                            {{ $user->role->value }}
                                        </span>
                                    </td>
                                    <td class="px-8 py-5 text-right whitespace-nowrap">
                                        <div class="flex items-center justify-end gap-2">
                                            @if($user->trashed())
                                                <button title="Restore User" wire:click="restore({{ $user->id }})" 
                                                    class="group/btn inline-flex items-center justify-center p-2.5 rounded-xl text-emerald-500 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 border-2 border-transparent hover:border-emerald-200 dark:hover:border-emerald-800 transition-all duration-300 hover:scale-110 active:scale-95 hover:shadow-md">
                                                    <svg class="w-4 h-4 group-hover/btn:rotate-180 transition-transform duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                    </svg>
                                                </button>
                                                
                                                @if(auth()->id() !== $user->id)
                                                    <button title="Permanent Delete" wire:click="forceDelete({{ $user->id }})" wire:confirm="Purge from system? This cannot be undone." 
                                                        class="group/del inline-flex items-center justify-center p-2.5 rounded-xl text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-900/20 border-2 border-transparent hover:border-rose-200 dark:hover:border-rose-800 transition-all duration-300 hover:scale-110 active:scale-95 hover:shadow-md">
                                                        <svg class="w-4 h-4 group-hover/del:animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7a4 4 0 11-8 0 4 4 0 018 0zM9 14a6 6 0 00-6 6v1h12v-1a6 6 0 00-6-6zM21 12h-6" />
                                                        </svg>
                                                    </button>
                                                @endif
                                            @else
                                                <button title="Edit User" wire:click="edit({{ $user->id }})" 
                                                    class="group/edit inline-flex items-center justify-center p-2.5 rounded-xl text-neutral-400 hover:text-neutral-900 dark:hover:text-white hover:bg-neutral-100 dark:hover:bg-neutral-800 border-2 border-transparent hover:border-neutral-200 dark:hover:border-neutral-700 transition-all duration-300 hover:scale-110 active:scale-95">
                                                    <svg class="w-4 h-4 group-hover/edit:rotate-12 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                    </svg>
                                                </button>

                                                @if(auth()->id() !== $user->id)
                                                    <button title="Archive User" wire:click="delete({{ $user->id }})" wire:confirm="Archive this user?" 
                                                        class="group/archive inline-flex items-center justify-center p-2.5 rounded-xl text-neutral-400 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-900/20 border-2 border-transparent hover:border-rose-200 dark:hover:border-rose-800 transition-all duration-300 hover:scale-110 active:scale-95">
                                                        <svg class="w-4 h-4 group-hover/archive:scale-90 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                                                        </svg>
                                                    </button>
                                                @else
                                                    <div title="System Lock (Self)" class="inline-flex items-center justify-center p-2.5 rounded-xl text-neutral-200 dark:text-neutral-700 cursor-not-allowed opacity-50">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                                        </svg>
                                                    </div>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Pagination --}}
            <div class="flex justify-center">
                {{ $users->links() }}
            </div>
        </div>

        {{-- Modal with Premium Design --}}
        @if($showingModal)
            <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-md animate-fade-in" 
                 x-data x-on:keydown.escape.window="$wire.set('showingModal', false)">
                <div class="bg-white dark:bg-neutral-900 rounded-3xl p-8 w-full max-w-2xl border-2 border-neutral-200 dark:border-neutral-800 shadow-2xl animate-scale-in max-h-[90vh] overflow-y-auto">
                    <div class="flex items-start justify-between mb-6">
                        <div>
                            <h2 class="text-2xl font-black mb-1 dark:text-white bg-gradient-to-r from-neutral-900 to-neutral-600 dark:from-white dark:to-neutral-300 bg-clip-text text-transparent">
                                {{ $form->user ? 'Edit' : 'Register' }} User
                            </h2>
                            <p class="text-[9px] text-neutral-500 dark:text-neutral-400 font-black uppercase tracking-[0.2em] flex items-center gap-2">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                Employee Profile Details
                            </p>
                        </div>
                        <button wire:click="$set('showingModal', false)" class="p-2 rounded-xl text-neutral-400 hover:text-neutral-600 dark:hover:text-white hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-all duration-300 hover:scale-110 active:scale-95">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form wire:submit="save" class="space-y-6">
                        {{-- Profile Photo Section --}}
                        <div class="relative overflow-hidden p-6 bg-gradient-to-br from-neutral-50 to-white dark:from-neutral-800/50 dark:to-neutral-900 rounded-2xl border-2 border-dashed border-neutral-200 dark:border-neutral-700 group hover:border-neutral-300 dark:hover:border-neutral-600 transition-all duration-300">
                            <div class="flex items-center gap-5">
                                <div class="relative group-hover:scale-105 transition-transform duration-300">
                                    <div class="h-20 w-20 rounded-2xl overflow-hidden bg-neutral-200 dark:bg-neutral-700 border-2 border-white dark:border-neutral-800 shadow-lg">
                                        @if ($form->photo)
                                            <img src="{{ $form->photo->temporaryUrl() }}" class="h-full w-full object-cover">
                                        @elseif ($form->user?->profile_photo_path)
                                            <img src="{{ $form->user->profile_photo_url }}" class="h-full w-full object-cover">
                                        @else
                                            <div class="h-full w-full flex items-center justify-center text-neutral-400 text-sm font-bold">
                                                {{ $form->user?->initials() ?? '?' }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="absolute -bottom-1 -right-1 w-6 h-6 bg-neutral-900 dark:bg-white rounded-lg flex items-center justify-center shadow-md">
                                        <svg class="w-3.5 h-3.5 text-white dark:text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </div>
                                </div>
                                
                                <div class="flex-1 space-y-2">
                                    <label class="text-[9px] font-black uppercase text-neutral-500 dark:text-neutral-400 tracking-[0.15em]">Profile Picture</label>
                                    <input type="file" wire:model="form.photo" 
                                        class="block w-full text-xs text-neutral-600 dark:text-neutral-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-[9px] file:font-black file:uppercase file:tracking-wider file:bg-neutral-900 file:text-white dark:file:bg-white dark:file:text-black hover:file:opacity-80 file:transition-all file:cursor-pointer file:shadow-md hover:file:shadow-lg">
                                    @error('form.photo') <span class="text-rose-500 text-[10px] font-bold flex items-center gap-1 mt-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ $message }}
                                    </span> @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Form Fields --}}
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label class="text-[9px] font-black uppercase text-neutral-500 dark:text-neutral-400 tracking-[0.15em] flex items-center gap-2">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    Full Name
                                </label>
                                <input wire:model="form.name" type="text" placeholder="Full Name" 
                                    class="w-full border-2 border-neutral-200 dark:border-neutral-700 p-3.5 rounded-xl dark:bg-neutral-800 dark:text-white text-sm outline-none focus:ring-4 focus:ring-neutral-100 dark:focus:ring-neutral-800/50 focus:border-neutral-400 dark:focus:border-neutral-600 transition-all duration-300 font-semibold shadow-sm">
                                @error('form.name') <span class="text-rose-500 text-[10px] font-bold flex items-center gap-1 mt-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    {{ $message }}
                                </span> @enderror
                            </div>
                            <div class="space-y-2">
                                <label class="text-[9px] font-black uppercase text-neutral-500 dark:text-neutral-400 tracking-[0.15em] flex items-center gap-2">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                                    </svg>
                                    Staff ID
                                </label>
                                <input wire:model="form.employee_id" type="text" placeholder="Staff ID" 
                                    class="w-full border-2 border-neutral-200 dark:border-neutral-700 p-3.5 rounded-xl dark:bg-neutral-800 dark:text-white text-sm outline-none focus:ring-4 focus:ring-neutral-100 dark:focus:ring-neutral-800/50 focus:border-neutral-400 dark:focus:border-neutral-600 transition-all duration-300 font-semibold shadow-sm">
                                @error('form.employee_id') <span class="text-rose-500 text-[10px] font-bold flex items-center gap-1 mt-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    {{ $message }}
                                </span> @enderror
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="text-[9px] font-black uppercase text-neutral-500 dark:text-neutral-400 tracking-[0.15em] flex items-center gap-2">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                Email Address
                            </label>
                            <input wire:model="form.email" type="email" placeholder="Email Address" 
                                class="w-full border-2 border-neutral-200 dark:border-neutral-700 p-3.5 rounded-xl dark:bg-neutral-800 dark:text-white text-sm outline-none focus:ring-4 focus:ring-neutral-100 dark:focus:ring-neutral-800/50 focus:border-neutral-400 dark:focus:border-neutral-600 transition-all duration-300 font-semibold shadow-sm">
                            @error('form.email') <span class="text-rose-500 text-[10px] font-bold flex items-center gap-1 mt-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ $message }}
                            </span> @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label class="text-[9px] font-black uppercase text-neutral-500 dark:text-neutral-400 tracking-[0.15em] flex items-center gap-2">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                    Department
                                </label>
                                <input wire:model="form.department" type="text" placeholder="Department" 
                                    class="w-full border-2 border-neutral-200 dark:border-neutral-700 p-3.5 rounded-xl dark:bg-neutral-800 dark:text-white text-sm outline-none focus:ring-4 focus:ring-neutral-100 dark:focus:ring-neutral-800/50 focus:border-neutral-400 dark:focus:border-neutral-600 transition-all duration-300 font-semibold shadow-sm">
                                @error('form.department') <span class="text-rose-500 text-[10px] font-bold flex items-center gap-1 mt-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    {{ $message }}
                                </span> @enderror
                            </div>
                            <div class="space-y-2">
                                <label class="text-[9px] font-black uppercase text-neutral-500 dark:text-neutral-400 tracking-[0.15em] flex items-center gap-2">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    Position
                                </label>
                                <input wire:model="form.position" type="text" placeholder="Position" 
                                    class="w-full border-2 border-neutral-200 dark:border-neutral-700 p-3.5 rounded-xl dark:bg-neutral-800 dark:text-white text-sm outline-none focus:ring-4 focus:ring-neutral-100 dark:focus:ring-neutral-800/50 focus:border-neutral-400 dark:focus:border-neutral-600 transition-all duration-300 font-semibold shadow-sm">
                                @error('form.position') <span class="text-rose-500 text-[10px] font-bold flex items-center gap-1 mt-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    {{ $message }}
                                </span> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label class="text-[9px] font-black uppercase text-neutral-500 dark:text-neutral-400 tracking-[0.15em] flex items-center gap-2">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                    </svg>
                                    System Role
                                </label>
                                <select wire:model="form.role" 
                                    class="w-full border-2 border-neutral-200 dark:border-neutral-700 p-3.5 rounded-xl dark:bg-neutral-800 dark:text-white text-sm outline-none focus:ring-4 focus:ring-neutral-100 dark:focus:ring-neutral-800/50 focus:border-neutral-400 dark:focus:border-neutral-600 transition-all duration-300 font-semibold shadow-sm">
                                    <option value="employee">Employee</option>
                                    <option value="admin">Administrator</option>
                                </select>
                            </div>
                            <div class="space-y-2">
                                <label class="text-[9px] font-black uppercase text-neutral-500 dark:text-neutral-400 tracking-[0.15em] flex items-center gap-2">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                    Password
                                </label>
                                <input wire:model="form.password" type="password" placeholder="{{ $form->user ? 'Leave blank to keep' : 'Min 8 characters' }}" 
                                    class="w-full border-2 border-neutral-200 dark:border-neutral-700 p-3.5 rounded-xl dark:bg-neutral-800 dark:text-white text-sm outline-none focus:ring-4 focus:ring-neutral-100 dark:focus:ring-neutral-800/50 focus:border-neutral-400 dark:focus:border-neutral-600 transition-all duration-300 font-semibold shadow-sm">
                                @error('form.password') <span class="text-rose-500 text-[10px] font-bold flex items-center gap-1 mt-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    {{ $message }}
                                </span> @enderror
                            </div>
                        </div>

                        <div class="flex justify-end gap-3 pt-6 border-t-2 border-neutral-100 dark:border-neutral-800">
                            <button type="button" wire:click="$set('showingModal', false)" 
                                class="px-6 py-3 text-sm text-neutral-500 dark:text-neutral-400 font-black hover:text-neutral-800 dark:hover:text-white transition-all duration-300 hover:scale-105 active:scale-95 uppercase tracking-wider">
                                Cancel
                            </button>
                            <button type="submit" wire:loading.attr="disabled" 
                                class="group bg-gradient-to-br from-neutral-900 to-neutral-700 dark:from-white dark:to-neutral-200 dark:text-black text-white px-8 py-3 rounded-xl text-sm font-black shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-300 hover:scale-105 active:scale-95 flex items-center justify-center min-w-[180px] uppercase tracking-wider">
                                <span wire:loading.remove class="flex items-center gap-2">
                                    <svg class="w-4 h-4 transition-transform group-hover:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                    </svg>
                                    {{ $form->user ? 'Update Profile' : 'Register User' }}
                                </span>
                                <span wire:loading class="flex items-center gap-2">
                                    <svg class="animate-spin h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                    Processing...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <style>
                @keyframes fade-in {
                    from { opacity: 0; }
                    to { opacity: 1; }
                }
                @keyframes scale-in {
                    from { 
                        opacity: 0;
                        transform: scale(0.9);
                    }
                    to { 
                        opacity: 1;
                        transform: scale(1);
                    }
                }
                .animate-fade-in {
                    animation: fade-in 0.2s ease-out;
                }
                .animate-scale-in {
                    animation: scale-in 0.3s ease-out;
                }
            </style>
        @endif
    </div>
</div>
