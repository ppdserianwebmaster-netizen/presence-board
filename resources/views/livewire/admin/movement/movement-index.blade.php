{{-- resources/views/livewire/admin/movement/movement-index.blade.php --}}
<div>
    <div class="p-6">
        <div class="flex flex-col gap-6">
            {{-- Header Section --}}
            <div class="flex justify-between items-end">
                <div>
                    <h1 class="text-2xl font-bold dark:text-white">Movement Management</h1>
                    <p class="text-sm text-neutral-500 font-medium">Add or edit employee out-of-office records.</p>
                </div>
                <button wire:click="create" class="bg-neutral-900 dark:bg-white dark:text-black text-white px-4 py-2 rounded-lg text-sm font-bold shadow-sm hover:opacity-90 transition-all">
                    Add Manual Log
                </button>
            </div>

            {{-- Search Bar --}}
            <div class="relative max-w-xs">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-4 w-4 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search employee name..." 
                    class="w-full pl-10 pr-4 py-2.5 bg-neutral-100 dark:bg-neutral-800/50 border-transparent focus:bg-white dark:focus:bg-neutral-900 focus:ring-2 focus:ring-neutral-200 dark:focus:ring-neutral-700 transition-all rounded-xl text-sm dark:text-white outline-none">
            </div>

            {{-- Table Container --}}
            <div class="overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 shadow-sm">
                <table class="w-full text-left text-sm">
                    <thead class="bg-neutral-50 dark:bg-neutral-800/50 text-neutral-500 uppercase text-[10px] font-bold tracking-widest">
                        <tr>
                            <th class="px-6 py-4">Employee</th>
                            <th class="px-6 py-4">Status / Type</th>
                            <th class="px-6 py-4">Timeframe & Duration</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800">
                        @forelse($movements as $m)
                            <tr wire:key="{{ $m->id }}" 
                                class="group border-l-4 border-transparent hover:border-{{ $m->type->color() }}-500 hover:bg-neutral-50 dark:hover:bg-neutral-800/50 transition-all">
                                
                                <td class="px-6 py-4">
                                    <div class="font-bold text-neutral-900 dark:text-white">{{ $m->user->name }}</div>
                                    <div class="text-[10px] text-neutral-400 font-mono tracking-tighter">{{ $m->user->employee_id }}</div>
                                </td>

                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-tighter bg-{{ $m->type->color() }}-100 text-{{ $m->type->color() }}-700 dark:bg-{{ $m->type->color() }}-900/30 dark:text-{{ $m->type->color() }}-400">
                                        {{ $m->type->label() }}
                                    </span>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="text-xs font-medium text-neutral-600 dark:text-neutral-400 font-mono">
                                        {{ $m->started_at->format('d M, H:i') }} — {{ $m->ended_at?->format('d M, H:i') ?? 'Ongoing' }}
                                    </div>
                                    <div class="text-[10px] font-black uppercase text-neutral-400 mt-0.5">
                                        {{ $m->duration_label }}
                                    </div>
                                </td>

                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-1">
                                        <button title="Edit Log" wire:click="edit({{ $m->id }})" class="p-2 rounded-lg text-neutral-400 hover:text-neutral-900 dark:hover:text-white hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-all">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                            </svg>
                                        </button>
                                        <button title="Delete Log" wire:click="delete({{ $m->id }})" wire:confirm="Delete this record permanently?" class="p-2 rounded-lg text-neutral-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-900/20 transition-all">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-10 text-center text-neutral-400 italic font-medium">No movement records found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $movements->links() }}
            </div>
        </div>
    </div>

    {{-- Modal --}}
    @if($showingModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-neutral-950/60 backdrop-blur-sm" 
             x-data x-on:keydown.escape.window="$wire.set('showingModal', false)">
            <div class="bg-white dark:bg-neutral-900 rounded-2xl p-6 w-full max-w-lg border border-neutral-200 dark:border-neutral-800 shadow-2xl">
                <h2 class="text-xl font-bold mb-1 dark:text-white">{{ $form->movement ? 'Update' : 'Create' }} Log</h2>
                <p class="text-xs text-neutral-500 mb-6 font-medium uppercase tracking-widest">Manual Movement Override</p>
                
                <form wire:submit="save" class="space-y-4">
                    @if(!$form->movement)
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold uppercase text-neutral-400">Assign to Employee</label>
                        <select wire:model="form.user_id" class="w-full border border-neutral-200 p-2 rounded-lg dark:bg-neutral-800 dark:border-neutral-700 dark:text-white text-sm outline-none focus:ring-1 focus:ring-neutral-400">
                            <option value="">Choose Employee...</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                        @error('form.user_id') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                    </div>
                    @endif

                    <div class="space-y-1">
                        <label class="text-[10px] font-bold uppercase text-neutral-400">Movement Type</label>
                        <select wire:model="form.type" class="w-full border border-neutral-200 p-2 rounded-lg dark:bg-neutral-800 dark:border-neutral-700 dark:text-white text-sm outline-none focus:ring-1 focus:ring-neutral-400">
                            <option value="">Select Type...</option>
                            @foreach(\App\Enums\MovementType::cases() as $type)
                                <option value="{{ $type->value }}">{{ $type->label() }}</option>
                            @endforeach
                        </select>
                        @error('form.type') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold uppercase text-neutral-400">Started At</label>
                            <input type="datetime-local" wire:model="form.started_at" class="w-full border border-neutral-200 p-2 rounded-lg dark:bg-neutral-800 dark:border-neutral-700 dark:text-white text-xs outline-none focus:ring-1 focus:ring-neutral-400">
                            @error('form.started_at') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                        </div>
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold uppercase text-neutral-400">Ended At (Optional)</label>
                            <input type="datetime-local" wire:model="form.ended_at" class="w-full border border-neutral-200 p-2 rounded-lg dark:bg-neutral-800 dark:border-neutral-700 dark:text-white text-xs outline-none focus:ring-1 focus:ring-neutral-400">
                            @error('form.ended_at') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="text-[10px] font-bold uppercase text-neutral-400">Admin Remarks</label>
                        <textarea wire:model="form.remark" rows="3" placeholder="Reason for manual entry..." class="w-full border border-neutral-200 p-2 rounded-lg dark:bg-neutral-800 dark:border-neutral-700 dark:text-white text-sm outline-none focus:ring-1 focus:ring-neutral-400"></textarea>
                        @error('form.remark') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex justify-end gap-3 pt-6 border-t border-neutral-100 dark:border-neutral-800">
                        <button type="button" wire:click="$set('showingModal', false)" class="px-4 py-2 text-sm text-neutral-500 font-bold hover:text-neutral-800 dark:hover:text-white transition-colors">Cancel</button>
                        <button type="submit" wire:loading.attr="disabled" class="bg-neutral-900 dark:bg-white dark:text-black text-white px-8 py-2 rounded-xl text-sm font-black shadow-lg hover:opacity-90 disabled:opacity-50 transition-all min-w-[140px]">
                            <span wire:loading.remove>Save Movement</span>
                            <span wire:loading>Processing...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
