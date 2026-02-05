{{-- 
    ============================================================================
    EMPLOYEE DASHBOARD - LIVEWIRE COMPONENT VIEW
    ============================================================================
    
    Purpose: Employee self-service dashboard for movement/leave logging
    Stack: Laravel 12 + Livewire 3 + Alpine.js + PHP 8.4.17
    
    Livewire Features Used:
    - wire:model.live with debouncing for search
    - wire:click for action dispatching
    - wire:loading for state management
    - wire:key for DOM tracking in loops
    
    Alpine.js Features Used:
    - x-show for modal visibility
    - x-transition for animations
    - @click.self for backdrop clicks
    
    DOM Conflict Prevention:
    - Alpine handles UI state (modal open/close)
    - Livewire handles data operations (CRUD)
    - Unique wire:key on all loop items
    - No overlapping event handlers
    
    Last Updated: 2026-02-04
--}}

<div class="min-h-screen bg-gradient-to-br from-neutral-50 via-white to-neutral-50 dark:from-neutral-950 dark:via-neutral-900 dark:to-neutral-950 p-6">
    <div class="max-w-5xl mx-auto">
        <div class="flex flex-col gap-8">
            
            {{-- ================================================================
                HEADER SECTION
                ================================================================
                Page title and primary action button
            --}}
            <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6">
                <div class="space-y-2">
                    <div class="flex items-center gap-3">
                        {{-- Visual accent bar --}}
                        <div class="w-1.5 h-8 bg-gradient-to-b from-neutral-900 to-neutral-600 dark:from-white dark:to-neutral-400 rounded-full"></div>
                        
                        {{-- Page title with gradient text --}}
                        <h1 class="text-3xl font-black dark:text-white tracking-tight bg-gradient-to-r from-neutral-900 to-neutral-600 dark:from-white dark:to-neutral-300 bg-clip-text text-transparent">
                            Movement Log
                        </h1>
                    </div>
                    
                    {{-- Page description --}}
                    <p class="text-sm text-neutral-500 dark:text-neutral-400 font-semibold ml-5">
                        Record your out-of-office status or future leave
                    </p>
                </div>
                
                {{-- 
                    NEW MOVEMENT BUTTON
                    ==========================================
                    - wire:click: Triggers Livewire method to open modal
                    - Opens modal form for creating new movement log
                --}}
                <button 
                    wire:click="openMovementModal" 
                    class="group px-6 py-3 text-[10px] font-black uppercase tracking-widest bg-gradient-to-br from-neutral-900 to-neutral-700 dark:from-white dark:to-neutral-200 text-white dark:text-black rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105 active:scale-95"
                >
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4 transition-transform group-hover:rotate-90 duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                        </svg>
                        New Movement
                    </span>
                </button>
            </div>

            {{-- ================================================================
                SEARCH BAR
                ================================================================
                Live search with debouncing for personal log history
            --}}
            <div class="relative max-w-md">
                {{-- Search icon --}}
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-neutral-400 dark:text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                
                {{-- 
                    SEARCH INPUT
                    - wire:model.live.debounce.300ms: Updates after 300ms of no typing
                    - Reduces server requests while maintaining responsiveness
                --}}
                <input 
                    wire:model.live.debounce.300ms="search" 
                    type="text" 
                    placeholder="Search your logs..." 
                    class="w-full pl-12 pr-4 py-3.5 bg-white dark:bg-neutral-900 border-2 border-neutral-200 dark:border-neutral-800 focus:border-neutral-400 dark:focus:border-neutral-600 focus:ring-4 focus:ring-neutral-100 dark:focus:ring-neutral-800/50 transition-all duration-300 rounded-2xl text-sm dark:text-white outline-none font-medium placeholder:text-neutral-400 shadow-sm focus:shadow-md"
                >
            </div>

            {{-- ================================================================
                MOVEMENT HISTORY TABLE
                ================================================================
                Displays employee's movement logs with inline actions
            --}}
            <div class="overflow-hidden rounded-3xl border-2 border-neutral-100 dark:border-neutral-800 bg-white dark:bg-neutral-900 shadow-xl">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        
                        {{-- TABLE HEADER --}}
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
                        
                        {{-- TABLE BODY --}}
                        <tbody class="divide-y-2 divide-neutral-50 dark:divide-neutral-800/50">
                            {{-- 
                                MOVEMENT ROWS
                                ==========================================
                                - @forelse: Blade directive with empty state support
                                - wire:key: CRITICAL for Livewire DOM tracking
                                - Dynamic theming based on log type
                            --}}
                            @forelse($history as $log)
                                <tr 
                                    wire:key="log-{{ $log->id }}" 
                                    class="group hover:bg-gradient-to-r hover:from-{{ $log->type->color() }}-50/30 hover:via-transparent hover:to-{{ $log->type->color() }}-50/30 dark:hover:from-{{ $log->type->color() }}-900/10 dark:hover:via-transparent dark:hover:to-{{ $log->type->color() }}-900/10 transition-all duration-300"
                                >
                                    {{-- TYPE CELL --}}
                                    <td class="px-8 py-5">
                                        {{-- Movement type badge with dynamic theming --}}
                                        @if($log->type)
                                            @php $color = $log->type->color(); @endphp
                                            <span class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-[9px] font-black uppercase tracking-wider 
                                                bg-gradient-to-r from-{{ $color }}-100 to-{{ $color }}-50 
                                                text-{{ $color }}-700 
                                                dark:from-{{ $color }}-900/40 dark:to-{{ $color }}-900/20 
                                                dark:text-{{ $color }}-400 
                                                border-2 border-{{ $color }}-200 dark:border-{{ $color }}-800 
                                                shadow-sm group-hover:shadow-md group-hover:scale-105 transition-all duration-300">
                                                
                                                {{-- Status Dot --}}
                                                <div class="w-2 h-2 rounded-full bg-{{ $color }}-500 shadow-sm animate-pulse"></div>
                                                
                                                {{ $log->type->label() }}
                                            </span>
                                        @endif
                                    </td>
                                    
                                    {{-- PERIOD CELL --}}
                                    <td class="px-8 py-5">
                                        <div class="space-y-1">
                                            {{-- Start time with arrow indicator --}}
                                            <div class="font-black text-neutral-900 dark:text-white flex items-center gap-2 group-hover:text-{{ $log->type->color() }}-600 dark:group-hover:text-{{ $log->type->color() }}-400 transition-colors duration-300">
                                                <span>{{ $log->started_at->format('d M, H:i') }}</span>
                                                <svg class="w-4 h-4 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                                </svg>
                                            </div>
                                            
                                            {{-- End time (if exists) --}}
                                            @if($log->ended_at)
                                                <div class="text-sm font-semibold text-neutral-500 dark:text-neutral-400">
                                                    {{ $log->ended_at->format('d M, H:i') }}
                                                </div>
                                            @else
                                                {{-- Ongoing indicator --}}
                                                <div class="flex items-center gap-1.5">
                                                    <div class="w-1.5 h-1.5 rounded-full bg-{{ $log->type->color() }}-500 animate-pulse"></div>
                                                    <span class="text-[10px] font-bold text-{{ $log->type->color() }}-600 dark:text-{{ $log->type->color() }}-400 uppercase tracking-wider">
                                                        Ongoing
                                                    </span>
                                                </div>
                                            @endif
                                            
                                            {{-- Duration display --}}
                                            @if($log->ended_at)
                                                @php
                                                    $duration = $log->started_at->diffForHumans($log->ended_at, true);
                                                @endphp
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[9px] font-black uppercase tracking-wider bg-neutral-100 dark:bg-neutral-800 text-neutral-600 dark:text-neutral-400 w-fit">
                                                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    {{ $duration }}
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    
                                    {{-- REMARK CELL --}}
                                    <td class="px-8 py-5">
                                        @if($log->remark)
                                            <p class="text-sm font-medium text-neutral-600 dark:text-neutral-400 line-clamp-2">
                                                {{ $log->remark }}
                                            </p>
                                        @else
                                            {{-- No remark placeholder --}}
                                            <span class="text-xs italic text-neutral-400 dark:text-neutral-500">
                                                No remark provided
                                            </span>
                                        @endif
                                    </td>
                                    
                                    {{-- ACTIONS CELL --}}
                                    <td class="px-8 py-5">
                                        <div class="flex items-center justify-end gap-2">
                                            {{-- 
                                                EDIT BUTTON
                                                - Only shown if log is editable
                                                - wire:click triggers edit modal
                                            --}}
                                            @if(!$log->ended_at)
                                                <button 
                                                    wire:click="edit({{ $log->id }})" 
                                                    class="p-2.5 rounded-xl hover:bg-{{ $log->type->color() }}-50 dark:hover:bg-{{ $log->type->color() }}-900/20 text-neutral-500 hover:text-{{ $log->type->color() }}-600 dark:hover:text-{{ $log->type->color() }}-400 transition-all duration-300 hover:scale-110 active:scale-95"
                                                    title="Edit log"
                                                >
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </button>
                                            @endif
                                            
                                            {{-- 
                                                DELETE BUTTON
                                                - wire:click triggers deletion
                                                - wire:confirm shows browser confirmation
                                            --}}
                                            <button 
                                                wire:click="delete({{ $log->id }})" 
                                                wire:confirm="Are you sure you want to delete this log entry?"
                                                class="p-2.5 rounded-xl hover:bg-rose-50 dark:hover:bg-rose-900/20 text-neutral-500 hover:text-rose-600 dark:hover:text-rose-400 transition-all duration-300 hover:scale-110 active:scale-95"
                                                title="Delete log"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                {{-- 
                                    EMPTY STATE
                                    ==========================================
                                    Displayed when no movement logs exist
                                --}}
                                <tr>
                                    <td colspan="4" class="px-8 py-16 text-center">
                                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-neutral-100 dark:bg-neutral-800 mb-4">
                                            <svg class="w-8 h-8 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        </div>
                                        <p class="text-sm font-semibold text-neutral-400 dark:text-neutral-500">
                                            No movement logs found
                                        </p>
                                        <p class="text-xs text-neutral-400 dark:text-neutral-600 mt-1">
                                            Click "New Movement" to create your first log entry
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                {{-- PAGINATION --}}
                <div class="px-8 py-6 border-t-2 border-neutral-100 dark:border-neutral-800">
                    {{ $history->links() }}
                </div>
            </div>
        </div>

        {{-- ================================================================
            MODAL - CREATE/EDIT MOVEMENT FORM
            ================================================================
            Conditional rendering based on $showingModal property
            Uses Livewire for data binding and Alpine for UI state
        --}}
        @if ($showingModal)
            {{-- Modal backdrop overlay --}}
            <div class="fixed inset-0 z-50 overflow-y-auto bg-neutral-900/60 dark:bg-black/80 backdrop-blur-sm flex items-center justify-center p-4 animate-fade-in">
                {{-- 
                    MODAL CONTENT CONTAINER
                    - @click.self: Only triggers when clicking the container (not children)
                    - Allows closing modal by clicking backdrop
                --}}
                <div 
                    @click.self="$wire.set('showingModal', false)" 
                    class="w-full max-w-2xl"
                >
                    <div class="bg-white dark:bg-neutral-900 rounded-3xl shadow-2xl border-2 border-neutral-100 dark:border-neutral-800 animate-scale-in">
                        
                        {{-- MODAL HEADER --}}
                        <div class="px-8 py-6 border-b-2 border-neutral-100 dark:border-neutral-800">
                            <div class="flex items-center justify-between">
                                <h2 class="text-2xl font-black text-neutral-900 dark:text-white flex items-center gap-3">
                                    <div class="w-1.5 h-6 bg-gradient-to-b from-neutral-900 to-neutral-600 dark:from-white dark:to-neutral-400 rounded-full"></div>
                                    {{ $form->movement ? 'Edit Movement' : 'New Movement Log' }}
                                </h2>
                                
                                {{-- Close button --}}
                                <button 
                                    wire:click="$set('showingModal', false)" 
                                    class="p-2 rounded-xl hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-all duration-300 hover:rotate-90"
                                >
                                    <svg class="w-6 h-6 text-neutral-600 dark:text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        
                        {{-- 
                            MODAL FORM
                            - wire:submit: Livewire form submission
                            - Prevents default form behavior
                        --}}
                        <form wire:submit="save" class="p-8 space-y-6">
                            
                            {{-- MOVEMENT TYPE SELECTION --}}
                            <div class="space-y-2">
                                <label class="text-[9px] font-black uppercase text-neutral-500 dark:text-neutral-400 tracking-[0.15em] flex items-center gap-2">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                    </svg>
                                    Movement Type
                                </label>
                                
                                {{-- 
                                    SELECT DROPDOWN
                                    - wire:model: Two-way data binding
                                    - Updates form.type property
                                --}}
                                <select 
                                    wire:model="form.type" 
                                    class="w-full border-2 border-neutral-200 dark:border-neutral-700 p-3.5 rounded-xl dark:bg-neutral-800 dark:text-white text-sm outline-none focus:ring-4 focus:ring-neutral-100 dark:focus:ring-neutral-800/50 focus:border-neutral-400 dark:focus:border-neutral-600 transition-all duration-300 font-semibold shadow-sm"
                                >
                                    <option value="">Select movement type...</option>
                                    <option value="meeting">Meeting</option>
                                    <option value="course">Training / Course</option>
                                    <option value="travel">Official Travel</option>
                                    <option value="leave">On Leave</option>
                                    <option value="other">Other / Out of Office</option>
                                </select>
                                
                                {{-- Validation error display --}}
                                @error('form.type') 
                                    <span class="text-rose-500 text-[10px] font-bold flex items-center gap-1 mt-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ $message }}
                                    </span> 
                                @enderror
                            </div>
                            
                            {{-- DATE/TIME INPUTS --}}
                            <div class="grid grid-cols-2 gap-4">
                                {{-- START DATE/TIME --}}
                                <div class="space-y-2">
                                    <label class="text-[9px] font-black uppercase text-neutral-500 dark:text-neutral-400 tracking-[0.15em] flex items-center gap-2">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        From
                                    </label>
                                    
                                    {{-- 
                                        DATETIME INPUT
                                        - HTML5 datetime-local picker
                                        - Consistent cross-browser experience
                                    --}}
                                    <input 
                                        type="datetime-local" 
                                        wire:model="form.started_at" 
                                        class="w-full border-2 border-neutral-200 dark:border-neutral-700 p-3.5 rounded-xl dark:bg-neutral-800 dark:text-white text-sm outline-none focus:ring-4 focus:ring-neutral-100 dark:focus:ring-neutral-800/50 focus:border-neutral-400 dark:focus:border-neutral-600 transition-all duration-300 font-semibold shadow-sm"
                                    >
                                    
                                    @error('form.started_at') 
                                        <span class="text-rose-500 text-[10px] font-bold flex items-center gap-1 mt-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            {{ $message }}
                                        </span> 
                                    @enderror
                                </div>
                                
                                {{-- END DATE/TIME --}}
                                <div class="space-y-2">
                                    <label class="text-[9px] font-black uppercase text-neutral-500 dark:text-neutral-400 tracking-[0.15em] flex items-center gap-2">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        Until
                                    </label>
                                    
                                    <input 
                                        type="datetime-local" 
                                        wire:model="form.ended_at" 
                                        class="w-full border-2 border-neutral-200 dark:border-neutral-700 p-3.5 rounded-xl dark:bg-neutral-800 dark:text-white text-sm outline-none focus:ring-4 focus:ring-neutral-100 dark:focus:ring-neutral-800/50 focus:border-neutral-400 dark:focus:border-neutral-600 transition-all duration-300 font-semibold shadow-sm"
                                    >
                                    
                                    @error('form.ended_at') 
                                        <span class="text-rose-500 text-[10px] font-bold flex items-center gap-1 mt-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            {{ $message }}
                                        </span> 
                                    @enderror
                                </div>
                            </div>

                            {{-- REMARK/PURPOSE TEXTAREA --}}
                            <div class="space-y-2">
                                <label class="text-[9px] font-black uppercase text-neutral-500 dark:text-neutral-400 tracking-[0.15em] flex items-center gap-2">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                                    </svg>
                                    Remark / Purpose
                                </label>
                                
                                {{-- 
                                    TEXTAREA
                                    - resize-none: Prevents manual resizing
                                    - rows="3": Default height
                                --}}
                                <textarea 
                                    wire:model="form.remark" 
                                    placeholder="e.g. Project meeting at Site A..." 
                                    rows="3" 
                                    class="w-full border-2 border-neutral-200 dark:border-neutral-700 p-3.5 rounded-xl dark:bg-neutral-800 dark:text-white text-sm outline-none focus:ring-4 focus:ring-neutral-100 dark:focus:ring-neutral-800/50 focus:border-neutral-400 dark:focus:border-neutral-600 transition-all duration-300 font-medium shadow-sm resize-none"
                                ></textarea>
                                
                                @error('form.remark') 
                                    <span class="text-rose-500 text-[10px] font-bold flex items-center gap-1 mt-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ $message }}
                                    </span> 
                                @enderror
                            </div>

                            {{-- FORM ACTION BUTTONS --}}
                            <div class="flex justify-end gap-3 pt-6 border-t-2 border-neutral-100 dark:border-neutral-800">
                                {{-- Cancel button --}}
                                <button 
                                    type="button" 
                                    wire:click="$set('showingModal', false)" 
                                    class="px-6 py-3 text-sm text-neutral-500 dark:text-neutral-400 font-black hover:text-neutral-800 dark:hover:text-white transition-all duration-300 hover:scale-105 active:scale-95 uppercase tracking-wider"
                                >
                                    Cancel
                                </button>
                                
                                {{-- Submit button with loading states --}}
                                <button 
                                    type="submit" 
                                    wire:loading.attr="disabled" 
                                    class="group bg-gradient-to-br from-neutral-900 to-neutral-700 dark:from-white dark:to-neutral-200 dark:text-black text-white px-8 py-3 rounded-xl text-sm font-black shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-300 hover:scale-105 active:scale-95 flex items-center justify-center min-w-[160px] uppercase tracking-wider"
                                >
                                    {{-- Button content when NOT loading --}}
                                    <span wire:loading.remove class="flex items-center gap-2">
                                        <svg class="w-4 h-4 transition-transform group-hover:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                        </svg>
                                        Save Log
                                    </span>
                                    
                                    {{-- Loading state --}}
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
            </div>

            {{-- 
                CUSTOM ANIMATIONS
                ================
                Inline styles for modal animations
                Better to move to app.css in production
            --}}
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
