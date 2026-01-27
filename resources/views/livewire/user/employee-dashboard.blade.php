{{-- resources/views/livewire/user/employee-dashboard.blade.php --}}
<div class="max-w-5xl mx-auto space-y-6 p-6">
    
    {{-- Main Action Header --}}
    <div class="bg-white dark:bg-neutral-900 p-8 rounded-[2rem] border border-neutral-200 dark:border-neutral-800 shadow-sm flex flex-col md:flex-row justify-between items-center gap-6">
        <div>
            <h1 class="text-2xl font-black dark:text-white leading-tight">Movement Log</h1>
            <p class="text-[10px] font-bold uppercase tracking-widest text-neutral-500 mt-1">Record your out-of-office status or future leave.</p>
        </div>
        <button wire:click="openMovementModal" class="w-full md:w-auto bg-neutral-900 dark:bg-white dark:text-black text-white px-10 py-4 rounded-2xl font-black text-sm transition-all shadow-lg active:scale-95">
            + NEW MOVEMENT
        </button>
    </div>

    {{-- Search Bar --}}
    <div class="relative max-w-xs">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <svg class="h-4 w-4 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
        </div>
        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search logs..." 
            class="w-full pl-10 pr-4 py-2.5 bg-neutral-100 dark:bg-neutral-800/50 border-transparent focus:bg-white dark:focus:bg-neutral-900 focus:ring-2 focus:ring-neutral-200 dark:focus:ring-neutral-700 transition-all rounded-xl text-sm dark:text-white outline-none">
    </div>

    {{-- Movements Table --}}
    <div class="bg-white dark:bg-neutral-900 rounded-[2rem] border border-neutral-200 dark:border-neutral-800 overflow-hidden shadow-sm">
        <table class="w-full text-left border-collapse table-fixed">
            <thead>
                <tr class="bg-neutral-50 dark:bg-neutral-800/50 border-b border-neutral-100 dark:border-neutral-800">
                    <th class="w-1/6 px-6 py-4 text-[10px] font-black uppercase text-neutral-400 tracking-widest">Type</th>
                    <th class="w-1/4 px-6 py-4 text-[10px] font-black uppercase text-neutral-400 tracking-widest">Period</th>
                    <th class="w-2/5 px-6 py-4 text-[10px] font-black uppercase text-neutral-400 tracking-widest">Remark</th>
                    <th class="w-20 px-6 py-4"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800">
                @forelse($history as $log)
                    <tr wire:key="{{ $log->id }}" class="group border-l-4 border-transparent hover:border-{{ $log->type->color() }}-500 hover:bg-neutral-50/50 dark:hover:bg-neutral-800/30 transition-all">
                        <td class="px-6 py-4 align-top">
                            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-{{ $log->type->color() }}-100 dark:bg-{{ $log->type->color() }}-900/30 text-{{ $log->type->color() }}-600 dark:text-{{ $log->type->color() }}-400 text-[10px] font-black uppercase tracking-tighter">
                                {{ $log->type->label() }}
                            </span>
                        </td>
                        <td class="px-6 py-4 align-top">
                            <div class="text-xs font-bold dark:text-white font-mono">
                                {{ $log->started_at->format('d M, H:i') }}
                                <span class="mx-1 text-neutral-300">→</span>
                                {{ $log->ended_at ? $log->ended_at->format('d M, H:i') : 'Present' }}
                            </div>
                            <div class="text-[10px] font-bold text-neutral-400 mt-1 uppercase tracking-tighter">{{ $log->duration_label }}</div>
                        </td>
                        
                        <td class="px-6 py-4 align-top">
                            <p class="text-xs text-neutral-600 dark:text-neutral-400 leading-relaxed whitespace-normal break-words">
                                {{ $log->remark ?: '-' }}
                            </p>
                        </td>

                        <td class="px-6 py-4 text-right align-top">
                            <button wire:click="deleteMovement({{ $log->id }})" wire:confirm="Remove this log?" 
                                class="p-2 rounded-lg text-neutral-300 hover:text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-900/20 transition-all opacity-0 group-hover:opacity-100">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-16 text-center text-neutral-400 italic text-sm font-medium">No movement records found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @if($history->hasPages())
            <div class="p-6 bg-neutral-50 dark:bg-neutral-800/20 border-t border-neutral-100 dark:border-neutral-800">
                {{ $history->links() }}
            </div>
        @endif
    </div>

    {{-- Create Modal --}}
    @if($showingModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-neutral-950/60 backdrop-blur-sm"
             x-data x-on:keydown.escape.window="$wire.set('showingModal', false)">
            <div class="bg-white dark:bg-neutral-900 rounded-[2.5rem] p-8 w-full max-w-md shadow-2xl border border-neutral-200 dark:border-neutral-800 transform transition-all">
                <h3 class="text-2xl font-black dark:text-white mb-1">Log Movement</h3>
                <p class="text-[10px] font-black text-neutral-400 uppercase tracking-widest mb-6">New entry record</p>
                
                <form wire:submit="submitMovement" class="space-y-5">
                    <div>
                        <label class="text-[10px] font-black text-neutral-400 uppercase tracking-widest px-1">Movement Type</label>
                        {{-- Updated to use form object --}}
                        <select wire:model="form.type" class="w-full bg-neutral-50 dark:bg-neutral-800 border-none rounded-2xl p-4 text-sm dark:text-white focus:ring-2 focus:ring-neutral-200 dark:focus:ring-neutral-700 transition-all mt-1 outline-none">
                            <option value="">Select...</option>
                            @foreach($types as $t) <option value="{{ $t->value }}">{{ $t->label() }}</option> @endforeach
                        </select>
                        @error('form.type') <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-[10px] font-black text-neutral-400 uppercase tracking-widest px-1">From</label>
                            <input type="datetime-local" wire:model="form.started_at" class="w-full bg-neutral-50 dark:bg-neutral-800 border-none rounded-2xl p-4 text-xs dark:text-white focus:ring-2 focus:ring-neutral-200 dark:focus:ring-neutral-700 transition-all mt-1 outline-none">
                            @error('form.started_at') <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="text-[10px] font-black text-neutral-400 uppercase tracking-widest px-1">Until</label>
                            <input type="datetime-local" wire:model="form.ended_at" class="w-full bg-neutral-50 dark:bg-neutral-800 border-none rounded-2xl p-4 text-xs dark:text-white focus:ring-2 focus:ring-neutral-200 dark:focus:ring-neutral-700 transition-all mt-1 outline-none">
                            @error('form.ended_at') <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="text-[10px] font-black text-neutral-400 uppercase tracking-widest px-1">Remark / Purpose</label>
                        <textarea wire:model="form.remark" placeholder="e.g. Project meeting at Site A..." class="w-full bg-neutral-50 dark:bg-neutral-800 border-none rounded-2xl p-4 text-sm dark:text-white h-28 focus:ring-2 focus:ring-neutral-200 dark:focus:ring-neutral-700 transition-all mt-1 resize-none outline-none"></textarea>
                        @error('form.remark') <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex gap-4 pt-4 border-t border-neutral-100 dark:border-neutral-800">
                        <button type="button" wire:click="$set('showingModal', false)" class="flex-1 font-bold text-neutral-400 hover:text-neutral-600 dark:hover:text-white transition-colors text-sm">Cancel</button>
                        <button type="submit" wire:loading.attr="disabled" class="flex-1 bg-neutral-900 dark:bg-white dark:text-black text-white py-4 rounded-2xl font-black text-sm shadow-xl hover:scale-[1.02] active:scale-95 transition-all disabled:opacity-50">
                            <span wire:loading.remove>SAVE LOG</span>
                            <span wire:loading>SAVING...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
