{{-- resources/views/livewire/user/employee-dashboard.blade.php --}}
<div class="max-w-5xl mx-auto p-6"> {{-- Matched root padding --}}
    <div class="flex flex-col gap-6">
        
        {{-- Header Section: Matched to Personnel Header --}}
        <div class="flex justify-between items-end">
            <div>
                <h1 class="text-2xl font-bold dark:text-white">Movement Log</h1>
                <p class="text-sm text-neutral-500 font-medium">Record your out-of-office status or future leave.</p>
            </div>
            <button wire:click="openMovementModal" class="bg-neutral-900 dark:bg-white dark:text-black text-white px-4 py-2 rounded-lg text-sm font-bold shadow-sm hover:opacity-90 transition-all">
                + New Movement
            </button>
        </div>

        {{-- Search Bar: Matched Personnel Search (Fixed visibility) --}}
        <div class="relative max-w-xs">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-4 w-4 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search logs..." 
                class="w-full pl-10 pr-4 py-2.5 bg-neutral-100 dark:bg-neutral-800/50 border-transparent focus:bg-white dark:focus:bg-neutral-900 focus:ring-2 focus:ring-neutral-200 dark:focus:ring-neutral-700 transition-all rounded-xl text-sm dark:text-white outline-none">
        </div>

        {{-- Table Container: Matched Personnel Table Styling --}}
        <div class="overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 shadow-sm">
            <table class="w-full text-left text-sm">
                <thead class="bg-neutral-50 dark:bg-neutral-800/50 text-neutral-500 uppercase text-[10px] font-bold tracking-widest">
                    <tr>
                        <th class="px-6 py-4">Type</th>
                        <th class="px-6 py-4">Period</th>
                        <th class="px-6 py-4">Remark</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800">
                    @forelse($history as $log)
                        <tr wire:key="{{ $log->id }}" class="hover:bg-neutral-50 dark:hover:bg-neutral-800/50 transition-colors">
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-tighter bg-{{ $log->type->color() }}-100 text-{{ $log->type->color() }}-700 dark:bg-{{ $log->type->color() }}-900/30 dark:text-{{ $log->type->color() }}-400">
                                    {{ $log->type->label() }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-neutral-900 dark:text-white">
                                    {{ $log->started_at->format('d M, H:i') }}
                                    <span class="mx-1 text-neutral-400">→</span>
                                    {{ $log->ended_at ? $log->ended_at->format('d M, H:i') : 'Present' }}
                                </div>
                                <div class="text-[10px] text-neutral-400 font-mono tracking-tighter uppercase">{{ $log->duration_label }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-neutral-600 dark:text-neutral-400 line-clamp-1 italic">
                                    {{ $log->remark ?: '-' }}
                                </p>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button title="Remove Log" wire:click="deleteMovement({{ $log->id }})" wire:confirm="Remove this log?" 
                                    class="p-2 rounded-lg text-neutral-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-900/20 transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-neutral-400 italic text-sm">No movement records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $history->links() }}
        </div>
    </div>

    {{-- Modal: Matched to Personnel Modal Styling --}}
    @if($showingModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-neutral-950/60 backdrop-blur-sm"
             x-data x-on:keydown.escape.window="$wire.set('showingModal', false)">
            <div class="bg-white dark:bg-neutral-900 rounded-2xl p-6 w-full max-w-lg border border-neutral-200 dark:border-neutral-800 shadow-2xl">
                <h2 class="text-xl font-bold mb-1 dark:text-white">Log Movement</h2>
                <p class="text-xs text-neutral-500 mb-6 font-medium uppercase tracking-widest">New entry record</p>
                
                <form wire:submit="submitMovement" class="space-y-4">
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold uppercase text-neutral-400">Movement Type</label>
                        <select wire:model="form.type" class="w-full border border-neutral-200 p-2 rounded-lg dark:bg-neutral-800 dark:border-neutral-700 dark:text-white text-sm outline-none focus:ring-1 focus:ring-neutral-400">
                            <option value="">Select...</option>
                            @foreach($types as $t) <option value="{{ $t->value }}">{{ $t->label() }}</option> @endforeach
                        </select>
                        @error('form.type') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold uppercase text-neutral-400">From</label>
                            <input type="datetime-local" wire:model="form.started_at" class="w-full border border-neutral-200 p-2 rounded-lg dark:bg-neutral-800 dark:border-neutral-700 dark:text-white text-sm outline-none focus:ring-1 focus:ring-neutral-400">
                            @error('form.started_at') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                        </div>
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold uppercase text-neutral-400">Until</label>
                            <input type="datetime-local" wire:model="form.ended_at" class="w-full border border-neutral-200 p-2 rounded-lg dark:bg-neutral-800 dark:border-neutral-700 dark:text-white text-sm outline-none focus:ring-1 focus:ring-neutral-400">
                            @error('form.ended_at') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="text-[10px] font-bold uppercase text-neutral-400">Remark / Purpose</label>
                        <textarea wire:model="form.remark" placeholder="e.g. Project meeting at Site A..." rows="3" class="w-full border border-neutral-200 p-2 rounded-lg dark:bg-neutral-800 dark:border-neutral-700 dark:text-white text-sm outline-none focus:ring-1 focus:ring-neutral-400"></textarea>
                        @error('form.remark') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex justify-end gap-3 pt-6 border-t border-neutral-100 dark:border-neutral-800">
                        <button type="button" wire:click="$set('showingModal', false)" class="px-4 py-2 text-sm text-neutral-500 font-bold hover:text-neutral-800 dark:hover:text-white transition-colors">Cancel</button>
                        <button type="submit" wire:loading.attr="disabled" class="bg-neutral-900 dark:bg-white dark:text-black text-white px-8 py-2 rounded-xl text-sm font-black shadow-lg hover:opacity-90 disabled:opacity-50 transition-all flex items-center justify-center min-w-[140px]">
                            <span wire:loading.remove>Save Log</span>
                            <span wire:loading>Saving...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
