{{-- 
    ============================================================================
    ADMIN DASHBOARD STATS - LIVEWIRE COMPONENT VIEW
    ============================================================================
    
    Purpose: Administrator dashboard with live statistics and activity feed
    Stack: Laravel 12 + Livewire 3 + Alpine.js + PHP 8.4.17
    
    Livewire Features Used:
    - Reactive properties for real-time stats
    - wire:navigate for SPA-like navigation
    - wire:key for proper DOM tracking in loops
    
    DOM Conflict Prevention:
    - No Alpine.js used (purely Livewire-driven)
    - All navigation uses wire:navigate
    - Unique wire:key on all loop items
    - No client-side state management needed
    
    Performance Optimizations:
    - Minimal JavaScript overhead
    - Server-side rendering for SEO
    - Efficient DOM updates via Livewire
    
    Last Updated: 2026-02-04
--}}

<div class="min-h-screen bg-gradient-to-br from-neutral-50 via-white to-neutral-50 dark:from-neutral-950 dark:via-neutral-900 dark:to-neutral-950 p-6">
    <div class="max-w-7xl mx-auto">
        
        {{-- ================================================================
            HEADER SECTION
            ================================================================
            Dashboard title, live status indicator, and quick action buttons
        --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-6">
            <div class="space-y-2">
                <div class="flex items-center gap-3">
                    {{-- Visual accent bar --}}
                    <div class="w-1.5 h-8 bg-gradient-to-b from-neutral-900 to-neutral-600 dark:from-white dark:to-neutral-400 rounded-full"></div>
                    
                    {{-- Page title with gradient text --}}
                    <h1 class="text-3xl font-black dark:text-white tracking-tight bg-gradient-to-r from-neutral-900 to-neutral-600 dark:from-white dark:to-neutral-300 bg-clip-text text-transparent">
                        Admin Overview
                    </h1>
                </div>
                
                {{-- Status indicators --}}
                <div class="flex items-center gap-3 ml-5">
                    {{-- Live status pulse indicator --}}
                    <div class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse shadow-lg shadow-emerald-500/50"></div>
                    
                    <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-neutral-500 dark:text-neutral-400">
                        Live System Status
                    </p>
                    
                    {{-- Separator --}}
                    <span class="text-neutral-300 dark:text-neutral-700">•</span>
                    
                    {{-- Current date display --}}
                    <p class="text-[10px] font-semibold text-neutral-400 dark:text-neutral-500">
                        {{ now()->format('D, d M Y') }}
                    </p>
                </div>
            </div>
            
            {{-- 
                QUICK ACTION BUTTONS
                =====================================
                - wire:navigate: Livewire SPA navigation (no page reload)
                - Uses route() helper for clean URLs
                - Grouped buttons for related actions
            --}}
            <div class="flex gap-3">
                {{-- Manage Users Button (Secondary Style) --}}
                <a 
                    href="{{ route('admin.users.index') }}" 
                    wire:navigate 
                    class="group px-6 py-3 text-[10px] font-black uppercase tracking-widest bg-white dark:bg-neutral-800 border-2 border-neutral-200 dark:border-neutral-700 rounded-2xl shadow-sm hover:shadow-md hover:border-neutral-300 dark:hover:border-neutral-600 transition-all duration-300 dark:text-white hover:scale-105 active:scale-95"
                >
                    <span class="flex items-center gap-2">
                        <svg class="w-3.5 h-3.5 transition-transform group-hover:rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        Manage Users
                    </span>
                </a>
                
                {{-- All Logs Button (Primary Style) --}}
                <a 
                    href="{{ route('admin.movements.index') }}" 
                    wire:navigate 
                    class="group px-6 py-3 text-[10px] font-black uppercase tracking-widest bg-gradient-to-br from-neutral-900 to-neutral-700 dark:from-white dark:to-neutral-200 text-white dark:text-black rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105 active:scale-95"
                >
                    <span class="flex items-center gap-2">
                        <svg class="w-3.5 h-3.5 transition-transform group-hover:rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        All Logs
                    </span>
                </a>
            </div>
        </div>

        {{-- ================================================================
            STATISTICS GRID
            ================================================================
            Dynamic statistics cards with hover effects and live data
            
            Layout:
            - Responsive grid (2 cols mobile, 3 tablet, 6 desktop)
            - First card featured (Total Staff)
            - Dynamic cards from typeCounts array
        --}}
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-10">
            
            {{-- 
                TOTAL STAFF CARD (Featured)
                ==========================================
                Special styling to highlight most important metric
            --}}
            <div class="group relative overflow-hidden p-6 bg-gradient-to-br from-neutral-900 via-neutral-800 to-neutral-900 dark:from-white dark:via-neutral-50 dark:to-white rounded-3xl shadow-xl hover:shadow-2xl transition-all duration-500 hover:scale-105 cursor-pointer border border-neutral-700 dark:border-neutral-200">
                {{-- Gradient overlay on hover --}}
                <div class="absolute inset-0 bg-gradient-to-tr from-transparent via-white/5 to-white/10 dark:via-black/5 dark:to-black/10 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                
                <div class="relative">
                    {{-- Card label --}}
                    <div class="text-[9px] font-black uppercase text-neutral-400 dark:text-neutral-500 tracking-[0.25em] mb-2 flex items-center gap-2">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Total Staff
                    </div>
                    
                    {{-- 
                        MAIN METRIC
                        - Large font for emphasis
                        - Scale animation on hover
                        - {{ $totalEmployees }}: Livewire reactive property
                    --}}
                    <div class="text-4xl font-black text-white dark:text-black leading-none mb-1 group-hover:scale-110 transition-transform duration-300">
                        {{ $totalEmployees }}
                    </div>
                    
                    {{-- Descriptor text --}}
                    <div class="text-[9px] font-bold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">
                        Active Personnel
                    </div>
                </div>
            </div>

            {{-- 
                DYNAMIC TYPE STATISTICS CARDS
                ==========================================
                Loop through movement types with counts
                
                Important Notes:
                - wire:key REQUIRED for proper DOM tracking
                - Dynamic colors from $stat['color']
                - Conditional animations based on count
            --}}
            @foreach($typeCounts as $stat)
                <div 
                    wire:key="stat-{{ $stat['label'] }}" 
                    class="group relative overflow-hidden p-6 bg-white dark:bg-neutral-900 border-2 border-neutral-100 dark:border-neutral-800 rounded-3xl shadow-sm hover:shadow-xl transition-all duration-500 hover:scale-105 hover:border-{{ $stat['color'] }}-200 dark:hover:border-{{ $stat['color'] }}-800 cursor-pointer"
                >
                    {{-- 
                        GRADIENT OVERLAY
                        - Uses stat color for themed hover effect
                        - Opacity transition for smooth appearance
                    --}}
                    <div class="absolute inset-0 bg-gradient-to-br from-{{ $stat['color'] }}-50/50 to-transparent dark:from-{{ $stat['color'] }}-950/30 dark:to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    
                    <div class="relative">
                        {{-- 
                            STATUS INDICATOR
                            - Animated pulse when count > 0
                            - Static with opacity when count = 0
                            - Ping animation for active states
                        --}}
                        <div class="flex items-center gap-2.5 mb-3">
                            <div class="relative">
                                {{-- Main pulse dot --}}
                                <div class="w-2.5 h-2.5 rounded-full bg-{{ $stat['color'] }}-500 {{ $stat['count'] > 0 ? 'animate-pulse' : 'opacity-30' }} shadow-lg shadow-{{ $stat['color'] }}-500/50"></div>
                                
                                {{-- Ping animation ring (only when active) --}}
                                @if($stat['count'] > 0)
                                    <div class="absolute inset-0 w-2.5 h-2.5 rounded-full bg-{{ $stat['color'] }}-500 animate-ping opacity-75"></div>
                                @endif
                            </div>
                            
                            {{-- Type label --}}
                            <div class="text-[9px] font-black uppercase text-neutral-600 dark:text-neutral-400 tracking-[0.15em]">
                                {{ $stat['label'] }}
                            </div>
                        </div>
                        
                        {{-- 
                            COUNT DISPLAY
                            - Color changes on hover to match type theme
                            - Scale animation on hover
                        --}}
                        <div class="text-4xl font-black dark:text-white leading-none mb-1 transition-all duration-300 group-hover:text-{{ $stat['color'] }}-600 dark:group-hover:text-{{ $stat['color'] }}-400 group-hover:scale-110">
                            {{ $stat['count'] }}
                        </div>
                        
                        {{-- Pluralization logic --}}
                        <div class="text-[9px] font-bold text-neutral-400 dark:text-neutral-500 uppercase tracking-wider">
                            {{ $stat['count'] === 1 ? 'Person' : 'People' }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- ================================================================
            RECENT ACTIVITY FEED
            ================================================================
            Real-time feed of employee movements
            
            Features:
            - Live updates via Livewire
            - Click-through to detailed logs
            - Empty state handling
        --}}
        <div class="bg-white dark:bg-neutral-900 border-2 border-neutral-100 dark:border-neutral-800 rounded-3xl overflow-hidden shadow-xl backdrop-blur-sm">
            
            {{-- FEED HEADER --}}
            <div class="px-8 py-5 border-b-2 border-neutral-100 dark:border-neutral-800 flex justify-between items-center bg-gradient-to-r from-neutral-50/80 via-white to-neutral-50/80 dark:from-neutral-800/50 dark:via-neutral-900 dark:to-neutral-800/50">
                <div class="flex items-center gap-3">
                    {{-- Icon badge --}}
                    <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-indigo-500 to-indigo-600 dark:from-indigo-400 dark:to-indigo-500 flex items-center justify-center shadow-lg shadow-indigo-500/30">
                        <svg class="w-4 h-4 text-white dark:text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    
                    <span class="text-[10px] font-black uppercase tracking-[0.2em] dark:text-white">
                        Recent Activity Feed
                    </span>
                </div>
                
                {{-- Personnel count badge --}}
                <span class="px-4 py-2 rounded-2xl text-[9px] font-black uppercase tracking-wider bg-gradient-to-r from-indigo-100 to-indigo-50 text-indigo-700 dark:from-indigo-900/40 dark:to-indigo-900/20 dark:text-indigo-300 border-2 border-indigo-200 dark:border-indigo-800 shadow-sm">
                    {{ $totalOutNow }} Personnel Out
                </span>
            </div>
            
            {{-- 
                ACTIVITY ITEMS
                ==========================================
                Loop through recent activities with proper key tracking
            --}}
            <div class="divide-y-2 divide-neutral-50 dark:divide-neutral-800/50">
                @forelse($recentActivity as $activity)
                    {{-- 
                        ACTIVITY ROW
                        - wire:key: CRITICAL for Livewire DOM tracking
                        - Format: "activity-{id}" for uniqueness
                        - Hover effects for interactivity
                    --}}
                    <div 
                        wire:key="activity-{{ $activity->id }}" 
                        class="group px-8 py-5 flex justify-between items-center hover:bg-gradient-to-r hover:from-neutral-50/50 hover:via-transparent hover:to-neutral-50/50 dark:hover:from-neutral-800/30 dark:hover:via-transparent dark:hover:to-neutral-800/30 transition-all duration-300 cursor-pointer"
                    >
                        <div class="flex items-center gap-5">
                            
                            {{-- 
                                USER AVATAR
                                - Dynamic colors based on activity type
                                - Shows user initials
                                - Status indicator dot
                            --}}
                            <div class="relative group-hover:scale-110 transition-transform duration-300">
                                {{-- Avatar container with dynamic theming --}}
                                <div class="h-12 w-12 rounded-2xl border-2 border-{{ $activity->type->color() }}-200 dark:border-{{ $activity->type->color() }}-800 bg-gradient-to-br from-{{ $activity->type->color() }}-50 to-{{ $activity->type->color() }}-100 dark:from-{{ $activity->type->color() }}-900/30 dark:to-{{ $activity->type->color() }}-900/10 flex items-center justify-center font-black text-sm text-{{ $activity->type->color() }}-700 dark:text-{{ $activity->type->color() }}-400 shadow-md group-hover:shadow-lg group-hover:shadow-{{ $activity->type->color() }}-500/20 transition-all duration-300">
                                    {{ $activity->user->initials() }}
                                </div>
                                
                                {{-- Status indicator dot --}}
                                <div class="absolute -top-1 -right-1 w-3 h-3 bg-{{ $activity->type->color() }}-500 rounded-full border-2 border-white dark:border-neutral-900 shadow-sm"></div>
                            </div>
                            
                            {{-- 
                                ACTIVITY DETAILS
                                - User name with color transition on hover
                                - Type badge and timestamp
                            --}}
                            <div class="space-y-1">
                                {{-- User name --}}
                                <div class="text-sm font-black text-neutral-900 dark:text-white group-hover:text-{{ $activity->type->color() }}-600 dark:group-hover:text-{{ $activity->type->color() }}-400 transition-colors duration-300">
                                    {{ $activity->user->name }}
                                </div>
                                
                                {{-- Type badge and time --}}
                                <div class="flex items-center gap-2.5">
                                    {{-- Movement type badge --}}
                                    @if ($activity->type)
                                        @php
                                            $color = $activity->type->color();
                                        @endphp
                                        
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-[9px] font-black uppercase tracking-wider 
                                            bg-{{ $color }}-100 dark:bg-{{ $color }}-900/30 
                                            text-{{ $color }}-700 dark:text-{{ $color }}-400 
                                            border border-{{ $color }}-200 dark:border-{{ $color }}-800 w-fit">
                                            
                                            {{-- Optional: Add the dynamic icon from your Enum --}}
                                            <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                            </svg>

                                            {{ $activity->type->label() }}
                                        </span>
                                    @endif
                                    
                                    {{-- Separator --}}
                                    <span class="text-[10px] font-semibold text-neutral-400 dark:text-neutral-500">
                                        • {{ $activity->started_at->diffForHumans() }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        {{-- 
                            VIEW LOG BUTTON
                            - wire:navigate: SPA navigation
                            - Pre-filtered search parameter
                            - Themed hover effects
                        --}}
                        <a 
                            href="{{ route('admin.movements.index', ['search' => $activity->user->name]) }}" 
                            wire:navigate 
                            class="group/btn px-5 py-2.5 text-[9px] font-black uppercase tracking-widest border-2 border-neutral-200 dark:border-neutral-700 rounded-xl text-neutral-500 hover:text-{{ $activity->type->color() }}-600 dark:hover:text-{{ $activity->type->color() }}-400 hover:bg-{{ $activity->type->color() }}-50 dark:hover:bg-{{ $activity->type->color() }}-900/20 hover:border-{{ $activity->type->color() }}-200 dark:hover:border-{{ $activity->type->color() }}-800 transition-all duration-300 hover:scale-105 active:scale-95 shadow-sm hover:shadow-md"
                        >
                            <span class="flex items-center gap-2">
                                View Log
                                {{-- Animated arrow on hover --}}
                                <svg class="w-3 h-3 transition-transform group-hover/btn:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                                </svg>
                            </span>
                        </a>
                    </div>
                @empty
                    {{-- 
                        EMPTY STATE
                        ==========================================
                        Displayed when no recent activity exists
                    --}}
                    <div class="px-8 py-16 text-center">
                        {{-- Icon --}}
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-neutral-100 dark:bg-neutral-800 mb-4">
                            <svg class="w-8 h-8 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                            </svg>
                        </div>
                        
                        {{-- Empty state message --}}
                        <p class="text-sm font-semibold text-neutral-400 dark:text-neutral-500">
                            No recent activity recorded today
                        </p>
                        <p class="text-xs text-neutral-400 dark:text-neutral-600 mt-1">
                            Activity will appear here once staff log movements
                        </p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
