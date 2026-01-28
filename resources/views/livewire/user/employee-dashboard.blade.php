{{-- resources/views/livewire/user/employee-dashboard.blade.php --}}
<div class="min-h-screen bg-gradient-to-br from-neutral-50 via-white to-neutral-50 dark:from-neutral-950 dark:via-neutral-900 dark:to-neutral-950 p-6">
    <div class="max-w-5xl mx-auto">
        <div class="flex flex-col gap-8">
            
            {{-- Header Section with Enhanced Design --}}
            <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6">
                <div class="space-y-2">
                    <div class="flex items-center gap-3">
                        <div class="w-1.5 h-8 bg-gradient-to-b from-neutral-900 to-neutral-600 dark:from-white dark:to-neutral-400 rounded-full"></div>
                        <h1 class="text-3xl font-black dark:text-white tracking-tight bg-gradient-to-r from-neutral-900 to-neutral-600 dark:from-white dark:to-neutral-300 bg-clip-text text-transparent">
                            Movement Log
                        </h1>
                    </div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400 font-semibold ml-5">
                        Record your out-of-office status or future leave
                    </p>
                </div>
                <button wire:click="openMovementModal" 
                    class="group px-6 py-3 text-[10px] font-black uppercase tracking-widest bg-gradient-to-br from-neutral-900 to-neutral-700 dark:from-white dark:to-neutral-200 text-white dark:text-black rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105 active:scale-95">
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4 transition-transform group-hover:rotate-90 duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                        </svg>
                        New Movement
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
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search your logs..." 
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
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                        </svg>
                                        Type
                                    </span>
                                </th>
                                <th class="px-8 py-5 text-left">
                                    <span class="flex items-center gap-2">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        Period
                                    </span>
                                </th>
                                <th class="px-8 py-5 text-left">
                                    <span class="flex items-center gap-2">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                                        </svg>
                                        Remark
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
                            @forelse($history as $log)
                                <tr wire:key="{{ $log->id }}" class="group hover:bg-gradient-to-r hover:from-{{ $log->type->color() }}-50/30 hover:via-transparent hover:to-{{ $log->type->color() }}-50/30 dark:hover:from-{{ $log->type->color() }}-900/10 dark:hover:via-transparent dark:hover:to-{{ $log->type->color() }}-900/10 transition-all duration-300">
                                    <td class="px-8 py-5">
                                        <span class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-[9px] font-black uppercase tracking-wider bg-gradient-to-r from-{{ $log->type->color() }}-100 to-{{ $log->type->color() }}-50 text-{{ $log->type->color() }}-700 dark:from-{{ $log->type->color() }}-900/40 dark:to-{{ $log->type->color() }}-900/20 dark:text-{{ $log->type->color() }}-400 border-2 border-{{ $log->type->color() }}-200 dark:border-{{ $log->type->color() }}-800 shadow-sm group-hover:shadow-md group-hover:scale-105 transition-all duration-300">
                                            <div class="w-2 h-2 rounded-full bg-{{ $log->type->color() }}-500 shadow-sm"></div>
                                            {{ $log->type->label() }}
                                        </span>
                                    </td>
                                    <td class="px-8 py-5">
                                        <div class="space-y-1">
                                            <div class="font-black text-neutral-900 dark:text-white flex items-center gap-2 group-hover:text-{{ $log->type->color() }}-600 dark:group-hover:text-{{ $log->type->color() }}-400 transition-colors duration-300">
                                                <span>{{ $log->started_at->format('d M, H:i') }}</span>
                                                <svg class="w-4 h-4 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                                </svg>
                                                <span>{{ $log->ended_at ? $log->ended_at->format('d M, H:i') : 'Present' }}</span>
                                            </div>
                                            <div class="text-[9px] text-neutral-400 dark:text-neutral-500 font-black tracking-wider uppercase flex items-center gap-2">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                {{ $log->duration_label }}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-5">
                                        <p class="text-sm text-neutral-600 dark:text-neutral-400 line-clamp-2 font-medium">
                                            {{ $log->remark ?: '—' }}
                                        </p>
                                    </td>
                                    <td class="px-8 py-5 text-right">
                                        <button title="Remove Log" wire:click="deleteMovement({{ $log->id }})" wire:confirm="Remove this log?" 
                                            class="group/del inline-flex items-center justify-center p-2.5 rounded-xl text-neutral-400 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-900/20 border-2 border-transparent hover:border-rose-200 dark:hover:border-rose-800 transition-all duration-300 hover:scale-110 active:scale-95 hover:shadow-md">
                                            <svg class="w-4 h-4 group-hover/del:animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-8 py-16 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-neutral-100 dark:bg-neutral-800 mb-4">
                                                <svg class="w-8 h-8 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                            </div>
                                            <p class="text-sm font-semibold text-neutral-400 dark:text-neutral-500">No movement records found</p>
                                            <p class="text-xs text-neutral-400 dark:text-neutral-600 mt-1">Click "New Movement" to create your first log</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Pagination --}}
            <div class="flex justify-center">
                {{ $history->links() }}
            </div>
        </div>
    </div>

    {{-- Modal with Premium Design --}}
    @if($showingModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-md animate-fade-in"
             x-data x-on:keydown.escape.window="$wire.set('showingModal', false)">
            <div class="bg-white dark:bg-neutral-900 rounded-3xl p-8 w-full max-w-xl border-2 border-neutral-200 dark:border-neutral-800 shadow-2xl animate-scale-in">
                <div class="flex items-start justify-between mb-6">
                    <div>
                        <h2 class="text-2xl font-black mb-1 dark:text-white bg-gradient-to-r from-neutral-900 to-neutral-600 dark:from-white dark:to-neutral-300 bg-clip-text text-transparent">Log Movement</h2>
                        <p class="text-[9px] text-neutral-500 dark:text-neutral-400 font-black uppercase tracking-[0.2em] flex items-center gap-2">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                            </svg>
                            New Entry Record
                        </p>
                    </div>
                    <button wire:click="$set('showingModal', false)" class="p-2 rounded-xl text-neutral-400 hover:text-neutral-600 dark:hover:text-white hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-all duration-300 hover:scale-110 active:scale-95">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                
                <form wire:submit="submitMovement" class="space-y-6">
                    <div class="space-y-2">
                        <label class="text-[9px] font-black uppercase text-neutral-500 dark:text-neutral-400 tracking-[0.15em] flex items-center gap-2">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                            </svg>
                            Movement Type
                        </label>
                        <select wire:model="form.type" class="w-full border-2 border-neutral-200 dark:border-neutral-700 p-3.5 rounded-xl dark:bg-neutral-800 dark:text-white text-sm outline-none focus:ring-4 focus:ring-neutral-100 dark:focus:ring-neutral-800/50 focus:border-neutral-400 dark:focus:border-neutral-600 transition-all duration-300 font-semibold shadow-sm">
                            <option value="">Select type...</option>
                            @foreach($types as $t) <option value="{{ $t->value }}">{{ $t->label() }}</option> @endforeach
                        </select>
                        @error('form.type') <span class="text-rose-500 text-[10px] font-bold flex items-center gap-1 mt-1">
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
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                From
                            </label>
                            <input type="datetime-local" wire:model="form.started_at" class="w-full border-2 border-neutral-200 dark:border-neutral-700 p-3.5 rounded-xl dark:bg-neutral-800 dark:text-white text-sm outline-none focus:ring-4 focus:ring-neutral-100 dark:focus:ring-neutral-800/50 focus:border-neutral-400 dark:focus:border-neutral-600 transition-all duration-300 font-semibold shadow-sm">
                            @error('form.started_at') <span class="text-rose-500 text-[10px] font-bold flex items-center gap-1 mt-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ $message }}
                            </span> @enderror
                        </div>
                        <div class="space-y-2">
                            <label class="text-[9px] font-black uppercase text-neutral-500 dark:text-neutral-400 tracking-[0.15em] flex items-center gap-2">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                Until
                            </label>
                            <input type="datetime-local" wire:model="form.ended_at" class="w-full border-2 border-neutral-200 dark:border-neutral-700 p-3.5 rounded-xl dark:bg-neutral-800 dark:text-white text-sm outline-none focus:ring-4 focus:ring-neutral-100 dark:focus:ring-neutral-800/50 focus:border-neutral-400 dark:focus:border-neutral-600 transition-all duration-300 font-semibold shadow-sm">
                            @error('form.ended_at') <span class="text-rose-500 text-[10px] font-bold flex items-center gap-1 mt-1">
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                            </svg>
                            Remark / Purpose
                        </label>
                        <textarea wire:model="form.remark" placeholder="e.g. Project meeting at Site A..." rows="3" class="w-full border-2 border-neutral-200 dark:border-neutral-700 p-3.5 rounded-xl dark:bg-neutral-800 dark:text-white text-sm outline-none focus:ring-4 focus:ring-neutral-100 dark:focus:ring-neutral-800/50 focus:border-neutral-400 dark:focus:border-neutral-600 transition-all duration-300 font-medium shadow-sm resize-none"></textarea>
                        @error('form.remark') <span class="text-rose-500 text-[10px] font-bold flex items-center gap-1 mt-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ $message }}
                        </span> @enderror
                    </div>

                    <div class="flex justify-end gap-3 pt-6 border-t-2 border-neutral-100 dark:border-neutral-800">
                        <button type="button" wire:click="$set('showingModal', false)" class="px-6 py-3 text-sm text-neutral-500 dark:text-neutral-400 font-black hover:text-neutral-800 dark:hover:text-white transition-all duration-300 hover:scale-105 active:scale-95 uppercase tracking-wider">
                            Cancel
                        </button>
                        <button type="submit" wire:loading.attr="disabled" class="group bg-gradient-to-br from-neutral-900 to-neutral-700 dark:from-white dark:to-neutral-200 dark:text-black text-white px-8 py-3 rounded-xl text-sm font-black shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-300 hover:scale-105 active:scale-95 flex items-center justify-center min-w-[160px] uppercase tracking-wider">
                            <span wire:loading.remove class="flex items-center gap-2">
                                <svg class="w-4 h-4 transition-transform group-hover:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                </svg>
                                Save Log
                            </span>
                            <span wire:loading class="flex items-center gap-2">
                                <svg class="animate-spin h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                Saving...
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
