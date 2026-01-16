{{-- resources/views/livewire/public-board/presence-board.blade.php --}}
@php use App\Models\Movement; @endphp

<div wire:poll.8s="rotatePage" class="min-h-screen bg-slate-950 relative overflow-hidden">

    {{-- Ambient Background Effects --}}
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-0 right-0 w-[1000px] h-[1000px] bg-blue-600/5 rounded-full blur-[120px]"></div>
        <div class="absolute bottom-0 left-0 w-[800px] h-[800px] bg-emerald-600/5 rounded-full blur-[120px]"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-purple-600/5 rounded-full blur-[100px]"></div>
    </div>

    {{-- Subtle Grid Pattern --}}
    <div class="absolute inset-0 opacity-[0.015]" style="background-image: linear-gradient(#60A5FA 1px, transparent 1px), linear-gradient(90deg, #60A5FA 1px, transparent 1px); background-size: 50px 50px;"></div>

    {{-- Screen width check --}}
    <div class="hidden min-[1280px]:flex h-screen flex-col relative z-10" id="board-container">

        {{-- Header Section --}}
        <header class="relative bg-slate-900/80 backdrop-blur-xl border-b border-slate-800/60 shadow-2xl">
            
            {{-- Accent Line --}}
            <div class="absolute top-0 left-0 right-0 h-[3px] bg-gradient-to-r from-blue-600 via-blue-500 to-emerald-500"></div>
            
            <div class="px-8 py-6">
                <div class="flex items-center justify-between">
                    
                    {{-- Left: Logo & Title --}}
                    <div class="flex items-center gap-5">
                        {{-- Logo Container --}}
                        <div class="relative w-20 h-20 group">
                            <div class="absolute inset-0 bg-gradient-to-br from-blue-600 to-blue-700 rounded-2xl blur-md opacity-50 group-hover:opacity-70 transition-opacity"></div>
                            <div class="relative w-20 h-20 bg-gradient-to-br from-blue-600 to-blue-700 rounded-2xl flex items-center justify-center shadow-lg shadow-blue-500/20 overflow-hidden border border-blue-500/30">
                                <div class="absolute inset-0 bg-gradient-to-tr from-white/0 via-white/10 to-white/0"></div>
                                {{-- Check if logo exists, otherwise use icon --}}
                                @if(file_exists(public_path('img/logo.png')))
                                    <img src="{{ asset('img/logo.png') }}" alt="Logo" class="relative z-10 w-12 h-12 object-contain drop-shadow-lg">
                                @else
                                    <i data-lucide="monitor-check" class="relative z-10 w-10 h-10 text-white" stroke-width="2.5"></i>
                                @endif
                            </div>
                        </div>
                        
                        <div>
                            <div class="flex items-center gap-3">
                                <h1 class="text-5xl font-bold text-slate-100 tracking-tight" style="font-family: 'Space Grotesk', sans-serif;">
                                    Presence Board
                                </h1>
                                <div class="flex items-center gap-2 px-3 py-1.5 bg-emerald-500/10 rounded-lg border border-emerald-500/20 backdrop-blur-sm">
                                    <span class="relative flex h-2.5 w-2.5">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-400 shadow-lg shadow-emerald-400/50"></span>
                                    </span>
                                    <span class="text-emerald-400 text-xs font-bold tracking-wider">LIVE</span>
                                </div>
                            </div>
                            <p class="text-slate-400 text-base font-medium mt-1 flex items-center gap-2">
                                <i data-lucide="activity" class="w-4 h-4"></i>
                                Real-time employee status monitoring
                            </p>
                        </div>
                    </div>

                    {{-- Right: Clock & Date --}}
                    <div class="text-right"
                         x-data="{ now: new Date() }"
                         x-init="setInterval(() => { now = new Date() }, 1000)">
                        <div class="text-6xl font-bold text-slate-100 tabular-nums tracking-tight drop-shadow-lg"
                             style="font-family: 'JetBrains Mono', monospace;"
                             x-text="now.toLocaleTimeString('en-GB', {hour:'2-digit',minute:'2-digit',second:'2-digit'})">
                        </div>
                        <div class="text-slate-400 text-lg font-medium mt-1 flex items-center justify-end gap-2">
                            <i data-lucide="calendar-days" class="w-5 h-5"></i>
                            <span x-text="now.toLocaleDateString('en-GB', {weekday: 'long', day:'2-digit', month:'short', year:'numeric'})"></span>
                        </div>
                    </div>

                </div>

                {{-- Stats Bar --}}
                <div class="mt-6 flex items-center gap-4 text-sm font-medium">
                    <div class="flex items-center gap-3 px-5 py-3 bg-gradient-to-br from-emerald-500/10 to-emerald-600/5 rounded-xl border border-emerald-500/20 backdrop-blur-sm shadow-lg shadow-emerald-500/5">
                        <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg shadow-emerald-500/30">
                            <i data-lucide="user-check" class="w-5 h-5 text-white" stroke-width="2.5"></i>
                        </div>
                        <div class="flex items-baseline gap-2">
                            <span class="text-3xl font-bold text-emerald-400">{{ $this->presentCount }}</span>
                            <span class="text-emerald-300/80 font-semibold">Present</span>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-3 px-5 py-3 bg-gradient-to-br from-amber-500/10 to-amber-600/5 rounded-xl border border-amber-500/20 backdrop-blur-sm shadow-lg shadow-amber-500/5">
                        <div class="w-10 h-10 bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl flex items-center justify-center shadow-lg shadow-amber-500/30">
                            <i data-lucide="plane" class="w-5 h-5 text-white" stroke-width="2.5"></i>
                        </div>
                        <div class="flex items-baseline gap-2">
                            <span class="text-3xl font-bold text-amber-400">{{ $this->awayCount }}</span>
                            <span class="text-amber-300/80 font-semibold">Away</span>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-3 px-5 py-3 bg-gradient-to-br from-blue-500/10 to-blue-600/5 rounded-xl border border-blue-500/20 backdrop-blur-sm shadow-lg shadow-blue-500/5">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-500/30">
                            <i data-lucide="users" class="w-5 h-5 text-white" stroke-width="2.5"></i>
                        </div>
                        <div class="flex items-baseline gap-2">
                            <span class="text-3xl font-bold text-blue-400">{{ $this->totalUsers }}</span>
                            <span class="text-blue-300/80 font-semibold">Total</span>
                        </div>
                    </div>
                    
                    <div class="flex-1"></div>
                    
                    <div class="flex items-center gap-2 text-slate-500 text-xs px-4 py-2 bg-slate-800/40 rounded-lg border border-slate-700/50">
                        <i data-lucide="refresh-cw" class="w-4 h-4 animate-spin" style="animation-duration: 3s;"></i>
                        <span>Updated <span class="font-mono text-slate-400">{{ now()->format('H:i:s') }}</span></span>
                    </div>
                </div>
            </div>
        </header>

        {{-- Main Content: Employee Cards Grid --}}
        <main class="flex-1 overflow-y-auto px-8 py-8 custom-scrollbar">
            <div class="grid grid-cols-1 
             min-[1440px]:grid-cols-3
             min-[1920px]:grid-cols-4
             min-[2560px]:grid-cols-5
             gap-6">
                
                @forelse($this->users as $user)
                    @php
                        $movement = $user->currentMovement;
                        $cardData = $this->getCardData($user, $movement);
                    @endphp

                    <div class="employee-card group" 
                         wire:key="employee-{{ $user->id }}"
                         data-status="{{ $cardData['statusType'] }}">
                        
                        {{-- Status Glow Effect --}}
                        <div class="status-glow" style="background: {{ $cardData['statusColor'] }}"></div>
                        
                        {{-- Card Content --}}
                        <div class="card-content">
                            
                            {{-- Header: Photo + Status Badge --}}
                            <div class="flex items-start justify-between mb-5">
                                <div class="relative">
                                    {{-- Photo Glow --}}
                                    <div class="absolute inset-0 rounded-2xl blur-xl opacity-40 transition-opacity group-hover:opacity-60"
                                         style="background: {{ $cardData['statusColor'] }}"></div>
                                    
                                    {{-- Photo --}}
                                    <img src="{{ $user->profilePhotoUrl }}" 
                                         alt="{{ $user->name }}"
                                         class="relative w-24 h-24 rounded-2xl object-cover shadow-2xl border-2 transition-transform group-hover:scale-105"
                                         style="border-color: {{ $cardData['statusColor'] }}">
                                    
                                    {{-- Status Icon Badge --}}
                                    <div class="absolute -bottom-2 -right-2 w-9 h-9 rounded-xl flex items-center justify-center shadow-lg border-2 border-slate-900 transition-transform group-hover:scale-110"
                                         style="background: {{ $cardData['statusColor'] }}">
                                        <i data-lucide="{{ $cardData['iconName'] }}" class="w-5 h-5 text-white" stroke-width="2.5"></i>
                                    </div>
                                </div>
                                
                                {{-- Status Badge --}}
                                <div class="px-3 py-1.5 rounded-lg text-xs font-bold tracking-wider shadow-lg border backdrop-blur-sm"
                                     style="background-color: {{ $cardData['badgeBg'] }}; color: {{ $cardData['badgeText'] }}; border-color: {{ $cardData['borderColor'] }}">
                                    {{ $cardData['badgeLabel'] }}
                                </div>
                            </div>

                            {{-- Employee Info --}}
                            <div class="space-y-1.5 mb-5">
                                <h3 class="text-2xl font-bold text-slate-100 leading-tight">
                                    {{ $user->name }}
                                </h3>
                                <p class="text-slate-300 text-base font-semibold flex items-center gap-2">
                                    <i data-lucide="briefcase" class="w-4 h-4 text-slate-400"></i>
                                    {{ $user->department }}
                                </p>
                                <p class="text-slate-500 text-sm font-medium flex items-center gap-2">
                                    <i data-lucide="user" class="w-3.5 h-3.5 text-slate-600"></i>
                                    {{ $user->position }}
                                </p>
                            </div>

                            {{-- Status Display --}}
                            <div class="pt-4 border-t border-slate-700/50">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <div class="w-2.5 h-2.5 rounded-full animate-pulse shadow-lg"
                                             style="background: {{ $cardData['statusColor'] }}; box-shadow: 0 0 10px {{ $cardData['statusColor'] }}"></div>
                                        <div class="text-base font-bold"
                                             style="color: {{ $cardData['statusColor'] }}">
                                            {{ $cardData['typeLabel'] }}
                                        </div>
                                    </div>
                                    @if($movement && $movement->ended_at)
                                        <div class="flex items-center gap-1.5 text-xs text-slate-400 font-medium bg-slate-800/50 px-2.5 py-1 rounded-lg border border-slate-700/50">
                                            <i data-lucide="clock" class="w-3.5 h-3.5"></i>
                                            <span class="font-mono">{{ $movement->ended_at->isToday() ? $movement->ended_at->format('H:i') : $movement->ended_at->format('d M') }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                        </div>
                    </div>

                @empty
                    <div class="col-span-full flex flex-col items-center justify-center py-24">
                        <div class="w-24 h-24 bg-slate-800/50 rounded-2xl flex items-center justify-center mb-6 border border-slate-700/50">
                            <i data-lucide="users-round" class="w-12 h-12 text-slate-600"></i>
                        </div>
                        <div class="text-3xl font-bold text-slate-300">No Employee Records</div>
                        <div class="text-slate-500 mt-2 text-base">Check back later for updates</div>
                    </div>
                @endforelse

            </div>
        </main>

        {{-- Footer --}}
        <footer class="relative bg-slate-900/80 backdrop-blur-xl border-t border-slate-800/60 px-8 py-4">
            <div class="flex items-center justify-between text-sm">
                <div class="flex items-center gap-4">
                    <span class="text-slate-300 font-semibold">
                        Presence Board <span class="text-blue-400 font-bold">v2.0</span>
                    </span>
                    <span class="text-slate-700">•</span>
                    <span class="text-slate-500 text-xs">© {{ date('Y') }} {{ config('app.name') }}</span>
                </div>

                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-2 text-slate-400">
                        <i data-lucide="file-text" class="w-4 h-4"></i>
                        <span class="font-medium">
                            Page <span class="text-blue-400">{{ $page }}</span> 
                            of <span class="text-blue-400">{{ $this->totalPages }}</span>
                        </span>
                    </div>
                    <span class="text-slate-700">•</span>
                    <div class="flex items-center gap-2 text-slate-500 text-xs">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-400 shadow-lg shadow-emerald-400/50"></span>
                        </span>
                        Auto-refresh active
                    </div>
                </div>
            </div>
        </footer>

    </div>

    {{-- Screen Size Warning --}}
    <div class="flex min-[1280px]:hidden w-full h-screen items-center justify-center bg-slate-950 px-8 relative z-10">
        <div class="text-center max-w-md">
            <div class="w-24 h-24 bg-slate-800/50 rounded-2xl flex items-center justify-center mx-auto mb-6 border border-slate-700/50">
                <i data-lucide="monitor-x" class="w-12 h-12 text-slate-500"></i>
            </div>
            <h2 class="text-4xl font-bold text-slate-200 mb-4">Display Not Optimized</h2>
            <p class="text-xl text-slate-400 mb-3">Minimum width required: <span class="font-bold text-blue-400">1280px</span></p>
            <p class="text-sm text-slate-500">Please use a larger display for optimal viewing experience</p>
        </div>
    </div>

</div>

@push('scripts')
<script>
    /**
     * Lucide Icon Re-Initialization
     *
     * Required because:
     * - Livewire v3 morphs DOM during wire:poll
     * - Icons must be recreated AFTER morphing completes
     */
    document.addEventListener('livewire:init', () => {

        const initLucide = () => {
            if (window.lucide) {
                lucide.createIcons();
            }
        };

        // Initial render
        initLucide();

        /**
         * CRITICAL:
         * Fires AFTER Livewire finishes morphing the DOM
         * This is the hook that fixes missing icons on auto-pagination
         */
        Livewire.hook('morph.updated', () => {
            initLucide();
        });
    });
</script>
@endpush

@push('styles')
<style>
    /* Employee Card Styling */
    .employee-card {
        position: relative;
        background: linear-gradient(135deg, rgba(30, 41, 59, 0.9) 0%, rgba(15, 23, 42, 0.95) 100%);
        border-radius: 1.5rem;
        overflow: hidden;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.3), 0 2px 4px -1px rgba(0, 0, 0, 0.2);
        transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.4s, border-color 0.4s;
        animation: fadeInUp 0.6s ease-out;
        border: 1px solid rgba(51, 65, 85, 0.6);
    }

    .employee-card:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.4), 0 10px 10px -5px rgba(0, 0, 0, 0.3);
        border-color: rgba(71, 85, 105, 0.8);
    }

    .status-glow {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        opacity: 0.8;
        box-shadow: 0 0 20px currentColor;
    }

    .card-content {
        padding: 1.75rem;
        padding-top: 2rem;
        position: relative;
    }

    /* Fade In Animation */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Custom Scrollbar */
    .custom-scrollbar::-webkit-scrollbar {
        width: 12px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
        background: rgba(15, 23, 42, 0.5);
        border-radius: 6px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: rgba(71, 85, 105, 0.6);
        border-radius: 6px;
        border: 2px solid rgba(15, 23, 42, 0.5);
    }

    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: rgba(100, 116, 139, 0.8);
    }

    /* Pulse Animation */
    @keyframes pulse {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: 0.5;
        }
    }

    .animate-pulse {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
    
    /* Ping Animation */
    @keyframes ping {
        75%, 100% {
            transform: scale(2);
            opacity: 0;
        }
    }
    
    .animate-ping {
        animation: ping 1.5s cubic-bezier(0, 0, 0.2, 1) infinite;
    }
    
    /* Smooth transitions for all icons */
    [data-lucide] {
        transition: all 0.3s ease;
    }
</style>
@endpush
