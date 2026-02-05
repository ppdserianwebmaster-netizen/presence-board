{{-- 
    ============================================================================
    USER INDEX - LIVEWIRE COMPONENT VIEW
    ============================================================================
    
    Purpose: Personnel management interface for viewing, creating, editing users
    Stack: Laravel 12 + Livewire 3 + Alpine.js + PHP 8.4.17
    
    Livewire Features Used:
    - wire:model.live (real-time reactive binding)
    - wire:click (action dispatching)
    - wire:loading (loading state management)
    - wire:key (DOM diffing optimization)
    
    Alpine.js Features Used:
    - x-show (conditional visibility with transitions)
    - x-transition (smooth animations)
    - @click.away (click outside detection)
    
    DOM Conflict Prevention:
    - All dropdowns use Alpine.js for client-side state (x-data)
    - Livewire handles server-side state and data mutations
    - Unique wire:key on all loop items for proper DOM tracking
    - No conflicting event listeners on same elements
    
    Last Updated: 2026-02-04
--}}

<div class="min-h-screen bg-gradient-to-br from-neutral-50 via-white to-neutral-50 dark:from-neutral-950 dark:via-neutral-900 dark:to-neutral-950 p-6">
    <div class="max-w-7xl mx-auto">
        <div class="flex flex-col gap-8">
            
            {{-- ================================================================
                HEADER SECTION
                ================================================================
                Contains page title and primary action button
            --}}
            <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6">
                <div class="space-y-2">
                    <div class="flex items-center gap-3">
                        {{-- Visual accent bar --}}
                        <div class="w-1.5 h-8 bg-gradient-to-b from-neutral-900 to-neutral-600 dark:from-white dark:to-neutral-400 rounded-full"></div>
                        
                        {{-- Page title with gradient text --}}
                        <h1 class="text-3xl font-black dark:text-white tracking-tight bg-gradient-to-r from-neutral-900 to-neutral-600 dark:from-white dark:to-neutral-300 bg-clip-text text-transparent">
                            Personnel
                        </h1>
                    </div>
                    
                    {{-- Page description --}}
                    <p class="text-sm text-neutral-500 dark:text-neutral-400 font-semibold ml-5">
                        Manage account access and roles
                    </p>
                </div>
                
                {{-- 
                    PRIMARY ACTION BUTTON
                    - wire:click triggers Livewire action
                    - wire:loading.attr prevents double-clicks
                    - wire:loading/wire:target for loading states
                --}}
                <button 
                    wire:click="create" 
                    wire:loading.attr="disabled" 
                    class="group px-6 py-3 text-[13px] font-black uppercase tracking-widest bg-neutral-900 dark:bg-white text-white dark:text-neutral-900 rounded-2xl hover:scale-[1.02] active:scale-[0.98] transition-all duration-300 shadow-xl shadow-neutral-200 dark:shadow-none flex items-center gap-3 disabled:opacity-50"
                >
                    {{-- Icon visible when NOT loading --}}
                    <svg wire:loading.remove wire:target="create" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" />
                    </svg>
                    
                    {{-- Button text when NOT loading --}}
                    <span wire:loading.remove wire:target="create">Add Personnel</span>
                    
                    {{-- Loading state with spinner --}}
                    <span wire:loading wire:target="create" class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Opening...
                    </span>
                </button>
            </div>

            {{-- ================================================================
                SEARCH BAR
                ================================================================
                Live search with debouncing to reduce server requests
            --}}
            <div class="relative max-w-md">
                {{-- Search icon container (absolute positioning) --}}
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    {{-- Search icon when NOT loading --}}
                    <svg wire:loading.remove wire:target="search" class="h-5 w-5 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    
                    {{-- Spinner when searching --}}
                    <svg wire:loading wire:target="search" class="animate-spin h-5 w-5 text-neutral-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
                
                {{-- 
                    SEARCH INPUT
                    - wire:model.live.debounce.300ms: Updates after 300ms of no typing
                    - wire:key: Unique identifier for Livewire DOM tracking
                --}}
                <input 
                    wire:model.live.debounce.300ms="search" 
                    wire:key="user-search-field" 
                    type="text" 
                    placeholder="Search personnel..." 
                    class="w-full pl-12 pr-4 py-3.5 bg-white dark:bg-neutral-900 border-neutral-200 dark:border-neutral-800 rounded-2xl focus:ring-2 focus:ring-neutral-900 dark:focus:ring-white transition-all duration-300 shadow-sm"
                >
            </div>

            {{-- ================================================================
                DATA TABLE
                ================================================================
                Displays user list with inline actions
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
                        
                        {{-- TABLE BODY --}}
                        <tbody class="divide-y-2 divide-neutral-50 dark:divide-neutral-800/50">
                            {{-- 
                                USER ROWS
                                - @foreach: Blade directive for looping
                                - wire:key: CRITICAL for Livewire DOM tracking in loops
                                - Prevents DOM conflicts during updates
                            --}}
                            @foreach ($users as $user)
                                <tr 
                                    wire:key="user-{{ $user->id }}" 
                                    class="group hover:bg-gradient-to-r hover:from-neutral-50/50 hover:via-transparent hover:to-neutral-50/50 dark:hover:from-neutral-800/30 dark:hover:via-transparent dark:hover:to-neutral-800/30 transition-all duration-300 {{ $user->trashed() ? 'opacity-50' : '' }}"
                                >
                                    {{-- EMPLOYEE INFO CELL --}}
                                    <td class="px-8 py-5">
                                        <div class="flex items-center gap-4">
                                            {{-- Avatar Section --}}
                                            <div class="relative group-hover:scale-110 transition-transform duration-300">
                                                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-neutral-900 to-neutral-600 dark:from-white dark:to-neutral-300 flex items-center justify-center shadow-lg overflow-hidden">
                                                    @if ($user->profile_photo_path)
                                                        {{-- Actual Profile Photo --}}
                                                        <img src="{{ $user->profile_photo_url }}" 
                                                            alt="{{ $user->name }}" 
                                                            class="w-full h-full object-cover shadow-inner">
                                                    @else
                                                        {{-- Initial Fallback (using your gradient) --}}
                                                        <span class="text-white dark:text-neutral-900 font-black text-lg">
                                                            {{ $user->initials() }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                            
                                            {{-- User details --}}
                                            <div class="flex flex-col">
                                                <span class="font-black text-neutral-900 dark:text-white text-base">
                                                    {{ $user->name }}
                                                </span>
                                                <span class="text-xs text-neutral-500 dark:text-neutral-400 font-semibold">
                                                    {{ $user->email }}
                                                </span>
                                                @if ($user->position)
                                                    <span class="text-[10px] text-neutral-400 dark:text-neutral-500 font-bold mt-0.5">
                                                        {{ $user->position }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    
                                    {{-- STATUS CELL --}}
                                    <td class="px-8 py-5">
                                        @if ($user->trashed())
                                            {{-- Deactivated status badge --}}
                                            <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full text-[10px] font-black uppercase tracking-wider bg-rose-100 dark:bg-rose-900/20 text-rose-700 dark:text-rose-400">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                                </svg>
                                                Deactivated
                                            </span>
                                        @else
                                            {{-- Active status badge --}}
                                            <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-100 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                Active
                                            </span>
                                        @endif
                                    </td>
                                    
                                    {{-- ROLE CELL --}}
                                    <td class="px-8 py-5">
                                        @if($user->is_admin)
                                            {{-- Admin role badge --}}
                                            <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full text-[10px] font-black uppercase tracking-wider bg-violet-100 dark:bg-violet-900/20 text-violet-700 dark:text-violet-400">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                                </svg>
                                                Administrator
                                            </span>
                                        @else
                                            {{-- Employee role badge --}}
                                            <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full text-[10px] font-black uppercase tracking-wider bg-neutral-100 dark:bg-neutral-800 text-neutral-700 dark:text-neutral-400">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                </svg>
                                                Employee
                                            </span>
                                        @endif
                                    </td>
                                    
                                    {{-- 
                                        ACTIONS CELL - DROPDOWN MENU
                                        ============================================
                                        DOM CONFLICT PREVENTION:
                                        - Uses Alpine.js (x-data) for client-side dropdown state
                                        - Livewire handles server actions (wire:click)
                                        - No conflicting event handlers
                                        - Each dropdown has isolated state
                                    --}}
                                    <td class="px-8 py-5">
                                        <div class="flex items-center justify-end gap-2">
                                            {{-- 
                                                ALPINE.JS DROPDOWN COMPONENT
                                                - x-data: Component state (open/closed)
                                                - @click.away: Close when clicking outside
                                                - x-show: Conditional rendering with transitions
                                            --}}
                                            <div class="relative" x-data="{ open: false }">
                                                {{-- Dropdown trigger button --}}
                                                <button 
                                                    @click="open = !open" 
                                                    class="p-2 rounded-xl hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-all duration-300 hover:scale-110 active:scale-95"
                                                >
                                                    <svg class="w-5 h-5 text-neutral-600 dark:text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                                    </svg>
                                                </button>
                                                
                                                {{-- 
                                                    DROPDOWN MENU
                                                    - x-show: Toggle visibility
                                                    - @click.away: Close on outside click
                                                    - x-transition: Smooth animations
                                                --}}
                                                <div 
                                                    x-show="open" 
                                                    @click.away="open = false"
                                                    x-transition:enter="transition ease-out duration-200"
                                                    x-transition:enter-start="opacity-0 scale-95"
                                                    x-transition:enter-end="opacity-100 scale-100"
                                                    x-transition:leave="transition ease-in duration-150"
                                                    x-transition:leave-start="opacity-100 scale-100"
                                                    x-transition:leave-end="opacity-0 scale-95"
                                                    class="absolute right-0 top-full mt-2 w-56 bg-white dark:bg-neutral-800 rounded-2xl shadow-2xl border-2 border-neutral-100 dark:border-neutral-700 overflow-hidden z-50"
                                                    style="display: none;"
                                                >
                                                    <div class="py-2">
                                                        {{-- Edit action --}}
                                                        <button 
                                                            wire:click="edit({{ $user->id }})" 
                                                            @click="open = false"
                                                            class="w-full px-5 py-3 text-left text-sm font-bold text-neutral-700 dark:text-neutral-300 hover:bg-neutral-50 dark:hover:bg-neutral-700/50 transition-all duration-200 flex items-center gap-3"
                                                        >
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                            </svg>
                                                            Edit User
                                                        </button>
                                                        
                                                        {{-- Conditional actions based on status --}}
                                                        @if ($user->trashed())
                                                            {{-- Restore action for deactivated users --}}
                                                            <button 
                                                                wire:click="restore({{ $user->id }})" 
                                                                @click="open = false"
                                                                class="w-full px-5 py-3 text-left text-sm font-bold text-emerald-700 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 transition-all duration-200 flex items-center gap-3"
                                                            >
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                                </svg>
                                                                Restore User
                                                            </button>
                                                        @else
                                                            {{-- Deactivate action for active users --}}
                                                            <button 
                                                                wire:click="delete({{ $user->id }})" 
                                                                @click="open = false"
                                                                class="w-full px-5 py-3 text-left text-sm font-bold text-rose-700 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-900/20 transition-all duration-200 flex items-center gap-3"
                                                            >
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                                </svg>
                                                                Deactivate User
                                                            </button>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                {{-- 
                    PAGINATION
                    - {{ $users->links() }}: Laravel pagination component
                    - Integrates with Livewire automatically
                --}}
                <div class="px-8 py-6 border-t-2 border-neutral-100 dark:border-neutral-800">
                    {{ $users->links() }}
                </div>
            </div>
        </div>

        {{-- ================================================================
            MODAL - CREATE/EDIT USER FORM
            ================================================================
            Conditional rendering based on $showingModal property
            Uses Livewire wire:model for two-way data binding
        --}}
        @if ($showingModal)
            {{-- Modal backdrop overlay --}}
            <div class="fixed inset-0 z-50 overflow-y-auto bg-neutral-900/60 dark:bg-black/80 backdrop-blur-sm flex items-center justify-center p-4 animate-fade-in">
                {{-- 
                    MODAL CONTENT CONTAINER
                    - @click.self: Only triggers when clicking the container itself (not children)
                    - Allows closing modal by clicking backdrop
                --}}
                <div 
                    @click.self="$wire.set('showingModal', false)" 
                    class="w-full max-w-2xl"
                >
                    <div class="bg-white dark:bg-neutral-900 rounded-3xl shadow-2xl border-2 border-neutral-100 dark:border-neutral-800 animate-scale-in">
                        {{-- Modal header --}}
                        <div class="px-8 py-6 border-b-2 border-neutral-100 dark:border-neutral-800">
                            <div class="flex items-center justify-between">
                                <h2 class="text-2xl font-black text-neutral-900 dark:text-white flex items-center gap-3">
                                    <div class="w-1.5 h-6 bg-gradient-to-b from-neutral-900 to-neutral-600 dark:from-white dark:to-neutral-400 rounded-full"></div>
                                    {{ $form->user ? 'Edit Personnel' : 'New Personnel' }}
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
                            {{-- PROFILE PHOTO UPLOAD SECTION --}}
                            <div class="flex flex-col items-center justify-center space-y-4 pb-4 border-b-2 border-neutral-100 dark:border-neutral-800">
                                <label class="text-[9px] font-black uppercase text-neutral-500 dark:text-neutral-400 tracking-[0.15em] self-start flex items-center gap-2">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    Profile Photo
                                </label>

                                <div class="relative group">
                                    {{-- Image Preview Container --}}
                                    <div class="w-24 h-24 rounded-2xl overflow-hidden border-4 border-white dark:border-neutral-800 shadow-xl bg-neutral-100 dark:bg-neutral-700">
                                        @if ($form->photo)
                                            {{-- Show temporary preview during upload --}}
                                            <img src="{{ $form->photo->temporaryUrl() }}" class="w-full h-full object-cover">
                                        @elseif ($form->user && $form->user->profile_photo_path)
                                            {{-- Show existing photo from storage --}}
                                            <img src="{{ Storage::url($form->user->profile_photo_path) }}" class="w-full h-full object-cover">
                                        @else
                                            {{-- Placeholder Icon --}}
                                            <div class="w-full h-full flex items-center justify-center text-neutral-400">
                                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                </svg>
                                            </div>
                                        @endif

                                        {{-- Loading Overlay --}}
                                        <div wire:loading wire:target="form.photo" class="absolute inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center">
                                            <svg class="animate-spin h-6 w-6 text-white" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                        </div>
                                    </div>

                                    {{-- Hidden File Input --}}
                                    <label class="absolute -bottom-2 -right-2 bg-white dark:bg-neutral-800 p-2 rounded-lg shadow-lg cursor-pointer hover:scale-110 transition-transform border border-neutral-100 dark:border-neutral-700">
                                        <svg class="w-4 h-4 text-neutral-600 dark:text-neutral-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <input type="file" wire:model="form.photo" class="hidden" accept="image/*">
                                    </label>
                                </div>

                                @error('form.photo') 
                                    <span class="text-rose-500 text-[10px] font-bold flex items-center gap-1">
                                        {{ $message }}
                                    </span> 
                                @enderror
                            </div>
                            
                            {{-- Full Name field --}}
                            <div class="space-y-2">
                                <label class="text-[9px] font-black uppercase text-neutral-500 dark:text-neutral-400 tracking-[0.15em] flex items-center gap-2">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    Full Name
                                </label>
                                
                                {{-- 
                                    TEXT INPUT
                                    - wire:model: Two-way data binding
                                    - Updates Livewire property on input
                                --}}
                                <input 
                                    wire:model="form.name" 
                                    type="text" 
                                    placeholder="Enter full name" 
                                    class="w-full border-2 border-neutral-200 dark:border-neutral-700 p-3.5 rounded-xl dark:bg-neutral-800 dark:text-white text-sm outline-none focus:ring-4 focus:ring-neutral-100 dark:focus:ring-neutral-800/50 focus:border-neutral-400 dark:focus:border-neutral-600 transition-all duration-300 font-semibold shadow-sm"
                                >
                                
                                {{-- Validation error display --}}
                                @error('form.name') 
                                    <span class="text-rose-500 text-[10px] font-bold flex items-center gap-1 mt-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ $message }}
                                    </span> 
                                @enderror
                            </div>
                            
                            {{-- Email field --}}
                            <div class="space-y-2">
                                <label class="text-[9px] font-black uppercase text-neutral-500 dark:text-neutral-400 tracking-[0.15em] flex items-center gap-2">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    Email Address
                                </label>
                                <input 
                                    wire:model="form.email" 
                                    type="email" 
                                    placeholder="employee@company.com" 
                                    class="w-full border-2 border-neutral-200 dark:border-neutral-700 p-3.5 rounded-xl dark:bg-neutral-800 dark:text-white text-sm outline-none focus:ring-4 focus:ring-neutral-100 dark:focus:ring-neutral-800/50 focus:border-neutral-400 dark:focus:border-neutral-600 transition-all duration-300 font-semibold shadow-sm"
                                >
                                @error('form.email') 
                                    <span class="text-rose-500 text-[10px] font-bold flex items-center gap-1 mt-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ $message }}
                                    </span> 
                                @enderror
                            </div>
                            
                            {{-- Position field --}}
                            <div class="space-y-2">
                                <label class="text-[9px] font-black uppercase text-neutral-500 dark:text-neutral-400 tracking-[0.15em] flex items-center gap-2">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    Position
                                </label>
                                <input 
                                    wire:model="form.position" 
                                    type="text" 
                                    placeholder="Position" 
                                    class="w-full border-2 border-neutral-200 dark:border-neutral-700 p-3.5 rounded-xl dark:bg-neutral-800 dark:text-white text-sm outline-none focus:ring-4 focus:ring-neutral-100 dark:focus:ring-neutral-800/50 focus:border-neutral-400 dark:focus:border-neutral-600 transition-all duration-300 font-semibold shadow-sm"
                                >
                                @error('form.position') 
                                    <span class="text-rose-500 text-[10px] font-bold flex items-center gap-1 mt-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ $message }}
                                    </span> 
                                @enderror
                            </div>
                            
                            {{-- Two-column layout for Role and Password --}}
                            <div class="grid grid-cols-2 gap-4">
                                {{-- System Role field --}}
                                <div class="space-y-2">
                                    <label class="text-[9px] font-black uppercase text-neutral-500 dark:text-neutral-400 tracking-[0.15em] flex items-center gap-2">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                        </svg>
                                        System Role
                                    </label>
                                    <select 
                                        wire:model="form.role" 
                                        class="w-full border-2 border-neutral-200 dark:border-neutral-700 p-3.5 rounded-xl dark:bg-neutral-800 dark:text-white text-sm outline-none focus:ring-4 focus:ring-neutral-100 dark:focus:ring-neutral-800/50 focus:border-neutral-400 dark:focus:border-neutral-600 transition-all duration-300 font-semibold shadow-sm"
                                    >
                                        <option value="employee">Employee</option>
                                        <option value="admin">Administrator</option>
                                    </select>
                                </div>
                                
                                {{-- Password field --}}
                                <div class="space-y-2">
                                    <label class="text-[9px] font-black uppercase text-neutral-500 dark:text-neutral-400 tracking-[0.15em] flex items-center gap-2">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                        </svg>
                                        Password
                                    </label>
                                    <input 
                                        wire:model="form.password" 
                                        type="password" 
                                        placeholder="{{ $form->user ? 'Leave blank to keep' : 'Min 8 characters' }}" 
                                        class="w-full border-2 border-neutral-200 dark:border-neutral-700 p-3.5 rounded-xl dark:bg-neutral-800 dark:text-white text-sm outline-none focus:ring-4 focus:ring-neutral-100 dark:focus:ring-neutral-800/50 focus:border-neutral-400 dark:focus:border-neutral-600 transition-all duration-300 font-semibold shadow-sm"
                                    >
                                    @error('form.password') 
                                        <span class="text-rose-500 text-[10px] font-bold flex items-center gap-1 mt-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            {{ $message }}
                                        </span> 
                                    @enderror
                                </div>
                            </div>
                            
                            {{-- Form action buttons --}}
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
                                    class="group bg-gradient-to-br from-neutral-900 to-neutral-700 dark:from-white dark:to-neutral-200 dark:text-black text-white px-8 py-3 rounded-xl text-sm font-black shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-300 hover:scale-105 active:scale-95 flex items-center justify-center min-w-[180px] uppercase tracking-wider"
                                >
                                    {{-- Button content when NOT loading --}}
                                    <span wire:loading.remove class="flex items-center gap-2">
                                        <svg class="w-4 h-4 transition-transform group-hover:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                        </svg>
                                        {{ $form->user ? 'Update Profile' : 'Register User' }}
                                    </span>
                                    
                                    {{-- Loading state --}}
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
