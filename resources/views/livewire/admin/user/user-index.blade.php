{{-- resources/views/livewire/admin/user/user-index.blade.php --}}
<div> {{-- Root Wrapper --}}
    <div class="p-6">
        <div class="flex flex-col gap-6">
            <div class="flex justify-between items-end">
                <div>
                    <h1 class="text-2xl font-bold dark:text-white">Personnel</h1>
                    <p class="text-sm text-neutral-500 font-medium">Manage account access and roles.</p>
                </div>
                <button wire:click="create" class="bg-neutral-900 dark:bg-white dark:text-black text-white px-4 py-2 rounded-lg text-sm font-bold shadow-sm hover:opacity-90 transition-all">
                    Add User
                </button>
            </div>

            <div class="relative max-w-xs">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-4 w-4 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search..." 
                    class="w-full pl-10 pr-4 py-2.5 bg-neutral-100 dark:bg-neutral-800/50 border-transparent focus:bg-white dark:focus:bg-neutral-900 focus:ring-2 focus:ring-neutral-200 dark:focus:ring-neutral-700 transition-all rounded-xl text-sm dark:text-white outline-none">
            </div>

            <div class="overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 shadow-sm">
                <table class="w-full text-left text-sm">
                    <thead class="bg-neutral-50 dark:bg-neutral-800/50 text-neutral-500 uppercase text-[10px] font-bold tracking-widest">
                        <tr>
                            <th class="px-6 py-4">Employee</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4">System Role</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800">
                        @foreach ($users as $user)
                            <tr wire:key="{{ $user->id }}" class="hover:bg-neutral-50 dark:hover:bg-neutral-800/50 transition-colors {{ $user->trashed() ? 'opacity-50' : '' }}">
                                <td class="px-6 py-4 flex items-center gap-3">
                                    <div class="h-9 w-9 rounded-xl bg-neutral-100 dark:bg-neutral-800 text-neutral-600 dark:text-neutral-300 flex items-center justify-center font-black text-xs border border-neutral-200 dark:border-neutral-700">
                                        {{ $user->initials() }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-neutral-900 dark:text-white">{{ $user->name }}</div>
                                        <div class="text-[10px] text-neutral-400 font-mono tracking-tighter">{{ $user->employee_id }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($user->trashed())
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-tighter bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">
                                            Archived
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-tighter bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">
                                            Active
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-tighter 
                                        {{ $user->role->value === 'admin' 
                                            ? 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400' 
                                            : 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' }}">
                                        {{ $user->role->value }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-2">
                                        @if($user->trashed())
                                            <button title="Restore User" wire:click="restore({{ $user->id }})" class="p-2 rounded-lg text-emerald-500 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 transition-all">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                            </button>
                                            
                                            @if(auth()->id() !== $user->id)
                                                <button title="Permanent Delete" wire:click="forceDelete({{ $user->id }})" wire:confirm="Purge from system? This cannot be undone." class="p-2 rounded-lg text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-900/20 transition-all">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7a4 4 0 11-8 0 4 4 0 018 0zM9 14a6 6 0 00-6 6v1h12v-1a6 6 0 00-6-6zM21 12h-6"></path></svg>
                                                </button>
                                            @endif
                                        @else
                                            <button title="Edit User" wire:click="edit({{ $user->id }})" class="p-2 rounded-lg text-neutral-400 hover:text-neutral-900 dark:hover:text-white hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-all">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                            </button>

                                            @if(auth()->id() !== $user->id)
                                                <button title="Archive User" wire:click="delete({{ $user->id }})" wire:confirm="Archive this user?" class="p-2 rounded-lg text-neutral-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-900/20 transition-all">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                                                </button>
                                            @else
                                                <div title="System Lock (Self)" class="p-2 text-neutral-200 dark:text-neutral-700 cursor-not-allowed">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 00-2 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
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

            <div class="mt-4">
                {{ $users->links() }}
            </div>
        </div>

        @if($showingModal)
            <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-neutral-950/60 backdrop-blur-sm" 
                 x-data x-on:keydown.escape.window="$wire.set('showingModal', false)">
                <div class="bg-white dark:bg-neutral-900 rounded-2xl p-6 w-full max-w-lg border border-neutral-200 dark:border-neutral-800 shadow-2xl">
                    <h2 class="text-xl font-bold mb-1 dark:text-white">{{ $form->user ? 'Edit' : 'Register' }} User</h2>
                    <p class="text-xs text-neutral-500 mb-6 font-medium uppercase tracking-widest">Employee Profile Details</p>

                    <form wire:submit="save" class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="text-[10px] font-bold uppercase text-neutral-400">Full Name</label>
                                <input wire:model="form.name" type="text" placeholder="Full Name" class="w-full border border-neutral-200 p-2 rounded-lg dark:bg-neutral-800 dark:border-neutral-700 dark:text-white text-sm outline-none focus:ring-1 focus:ring-neutral-400">
                                @error('form.name') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                            </div>
                            <div class="space-y-1">
                                <label class="text-[10px] font-bold uppercase text-neutral-400">Staff ID</label>
                                <input wire:model="form.employee_id" type="text" placeholder="Staff ID" class="w-full border border-neutral-200 p-2 rounded-lg dark:bg-neutral-800 dark:border-neutral-700 dark:text-white text-sm outline-none focus:ring-1 focus:ring-neutral-400">
                                @error('form.employee_id') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="space-y-1">
                            <label class="text-[10px] font-bold uppercase text-neutral-400">Email Address</label>
                            <input wire:model="form.email" type="email" placeholder="Email Address" class="w-full border border-neutral-200 p-2 rounded-lg dark:bg-neutral-800 dark:border-neutral-700 dark:text-white text-sm outline-none focus:ring-1 focus:ring-neutral-400">
                            @error('form.email') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="text-[10px] font-bold uppercase text-neutral-400">Department</label>
                                <input wire:model="form.department" type="text" placeholder="Department" class="w-full border border-neutral-200 p-2 rounded-lg dark:bg-neutral-800 dark:border-neutral-700 dark:text-white text-sm outline-none focus:ring-1 focus:ring-neutral-400">
                                @error('form.department') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                            </div>
                            <div class="space-y-1">
                                <label class="text-[10px] font-bold uppercase text-neutral-400">Position</label>
                                <input wire:model="form.position" type="text" placeholder="Position" class="w-full border border-neutral-200 p-2 rounded-lg dark:bg-neutral-800 dark:border-neutral-700 dark:text-white text-sm outline-none focus:ring-1 focus:ring-neutral-400">
                                @error('form.position') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="text-[10px] font-bold uppercase text-neutral-400">System Role</label>
                                <select wire:model="form.role" class="w-full border border-neutral-200 p-2 rounded-lg dark:bg-neutral-800 dark:border-neutral-700 dark:text-white text-sm outline-none focus:ring-1 focus:ring-neutral-400">
                                    <option value="employee">Employee</option>
                                    <option value="admin">Administrator</option>
                                </select>
                            </div>
                            <div class="space-y-1">
                                <label class="text-[10px] font-bold uppercase text-neutral-400">Password</label>
                                <input wire:model="form.password" type="password" placeholder="{{ $form->user ? 'Leave blank to keep' : 'Min 8 characters' }}" class="w-full border border-neutral-200 p-2 rounded-lg dark:bg-neutral-800 dark:border-neutral-700 dark:text-white text-sm outline-none focus:ring-1 focus:ring-neutral-400">
                                @error('form.password') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="flex justify-end gap-3 pt-6 border-t border-neutral-100 dark:border-neutral-800">
                            <button type="button" wire:click="$set('showingModal', false)" class="px-4 py-2 text-sm text-neutral-500 font-bold hover:text-neutral-800 dark:hover:text-white transition-colors">Cancel</button>
                            <button type="submit" wire:loading.attr="disabled" class="bg-neutral-900 dark:bg-white dark:text-black text-white px-8 py-2 rounded-xl text-sm font-black shadow-lg hover:opacity-90 disabled:opacity-50 transition-all flex items-center justify-center min-w-[140px]">
                                <span wire:loading.remove>{{ $form->user ? 'Update Profile' : 'Register User' }}</span>
                                <span wire:loading>Processing...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
</div> {{-- End Root --}}
