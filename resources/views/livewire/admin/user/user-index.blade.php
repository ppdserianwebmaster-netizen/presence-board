<div class="p-6">
    <div class="flex flex-col gap-6">
        <div class="flex justify-between items-end">
            <div>
                <h1 class="text-2xl font-bold dark:text-white">Personnel</h1>
                <p class="text-sm text-neutral-500">Manage account access and roles.</p>
            </div>
            <button wire:click="create" class="bg-black dark:bg-white dark:text-black text-white px-4 py-2 rounded-lg text-sm font-bold">Add User</button>
        </div>

        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search..." class="w-full max-w-xs p-2 rounded-lg border dark:bg-neutral-800 dark:border-neutral-700 dark:text-white">

        <div class="overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
            <table class="w-full text-left text-sm">
                <thead class="bg-neutral-50 dark:bg-neutral-800/50 dark:text-neutral-400">
                    <tr>
                        <th class="px-6 py-3">Employee</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700 dark:text-neutral-300">
                    @foreach ($users as $user)
                        <tr wire:key="{{ $user->id }}" class="{{ $user->trashed() ? 'opacity-50' : '' }}">
                            <td class="px-6 py-4 flex items-center gap-3">
                                <div class="h-8 w-8 rounded-full bg-neutral-200 dark:bg-neutral-700 flex items-center justify-center font-bold text-[10px]">{{ $user->initials() }}</div>
                                <div>
                                    <div class="font-bold">{{ $user->name }}</div>
                                    <div class="text-xs text-neutral-500">{{ $user->employee_id }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($user->trashed())
                                    <span class="text-red-500 text-xs font-bold uppercase">Archived</span>
                                @else
                                    <span class="text-green-500 text-xs font-bold uppercase">Active</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if($user->trashed())
                                    <button wire:click="restore({{ $user->id }})" class="text-blue-500 font-bold mr-2">Restore</button>
                                    <button wire:click="forceDelete({{ $user->id }})" wire:confirm="Wipe from DB?" class="text-red-700 font-bold">Purge</button>
                                @else
                                    <button wire:click="edit({{ $user->id }})" class="text-neutral-500 font-bold mr-2">Edit</button>
                                    <button wire:click="delete({{ $user->id }})" wire:confirm="Archive user?" class="text-red-500 font-bold">Archive</button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $users->links() }}
    </div>

    @if($showingModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-neutral-950/50 backdrop-blur-sm">
            <div class="relative w-full max-w-lg rounded-xl bg-white p-6 shadow-2xl dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-800">
                <h2 class="text-xl font-semibold text-neutral-900 dark:text-neutral-100">
                    {{ $form->user ? 'Edit User' : 'Register New User' }}
                </h2>
                <p class="text-sm text-neutral-500 mb-6">Details for {{ $form->name ?: 'the new employee' }}.</p>

                <form wire:submit="save" class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold text-neutral-400 uppercase tracking-wider">Full Name</label>
                            <input wire:model="form.name" type="text" class="w-full rounded-lg border border-neutral-200 bg-transparent px-3 py-2 text-sm focus:ring-1 focus:ring-neutral-500 outline-none dark:border-neutral-700 dark:text-neutral-100">
                            @error('form.name') <span class="text-[10px] text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold text-neutral-400 uppercase tracking-wider">Staff ID</label>
                            <input wire:model="form.employee_id" type="text" class="w-full rounded-lg border border-neutral-200 bg-transparent px-3 py-2 text-sm focus:ring-1 focus:ring-neutral-500 outline-none dark:border-neutral-700 dark:text-neutral-100">
                            @error('form.employee_id') <span class="text-[10px] text-red-500">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-neutral-400 uppercase tracking-wider">Email Address</label>
                        <input wire:model="form.email" type="email" class="w-full rounded-lg border border-neutral-200 bg-transparent px-3 py-2 text-sm focus:ring-1 focus:ring-neutral-500 outline-none dark:border-neutral-700 dark:text-neutral-100">
                        @error('form.email') <span class="text-[10px] text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold text-neutral-400 uppercase tracking-wider">Department</label>
                            <input wire:model="form.department" type="text" class="w-full rounded-lg border border-neutral-200 bg-transparent px-3 py-2 text-sm focus:ring-1 focus:ring-neutral-500 outline-none dark:border-neutral-700">
                        </div>
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold text-neutral-400 uppercase tracking-wider">Position</label>
                            <input wire:model="form.position" type="text" class="w-full rounded-lg border border-neutral-200 bg-transparent px-3 py-2 text-sm focus:ring-1 focus:ring-neutral-500 outline-none dark:border-neutral-700">
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-neutral-400 uppercase tracking-wider">System Role</label>
                        <select wire:model="form.role" class="w-full rounded-lg border border-neutral-200 bg-white dark:bg-neutral-800 px-3 py-2 text-sm outline-none dark:border-neutral-700 dark:text-neutral-100">
                            <option value="employee">Employee</option>
                            <option value="admin">Administrator</option>
                        </select>
                    </div>

                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-neutral-400 uppercase tracking-wider">Password</label>
                        <input wire:model="form.password" type="password" placeholder="{{ $form->user ? 'Leave blank to keep' : 'Min 8 characters' }}" class="w-full rounded-lg border border-neutral-200 bg-transparent px-3 py-2 text-sm focus:ring-1 focus:ring-neutral-500 outline-none dark:border-neutral-700 dark:text-neutral-100">
                        @error('form.password') <span class="text-[10px] text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex justify-end gap-3 pt-6 border-t border-neutral-100 dark:border-neutral-800">
                        <button type="button" wire:click="$set('showingModal', false)" class="px-4 py-2 text-sm text-neutral-500 font-medium">Cancel</button>
                        <button type="submit" wire:loading.attr="disabled" class="bg-neutral-900 dark:bg-neutral-100 text-white dark:text-neutral-900 px-6 py-2 rounded-lg text-sm font-semibold hover:opacity-90 disabled:opacity-50 transition-all">
                            <span wire:loading.remove>{{ $form->user ? 'Update' : 'Register' }}</span>
                            <span wire:loading>Processing...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
