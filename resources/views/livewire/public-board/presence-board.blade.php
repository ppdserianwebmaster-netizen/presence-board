{{-- resources/views/livewire/public-board/presence-board.blade.php --}}
@php use App\Models\Movement; @endphp

<div wire:poll.8s="rotatePage" class="min-h-screen bg-slate-950 text-white relative overflow-hidden">

    {{-- Screen width check --}}
    <div class="hidden min-[1280px]:flex h-screen flex-col" id="board-container">

        {{-- Header Section --}}
        <header class="bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 border-b-4 border-emerald-500 shadow-2xl relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-b from-transparent to-black/20"></div>
            
            <div class="relative px-8 py-6">
                <div class="flex items-center justify-between">
                    
                    {{-- Left: Logo & Title --}}
                    <div class="flex items-center gap-6">
                        <div class="w-20 h-20 bg-emerald-500 rounded-xl flex items-center justify-center shadow-lg ring-4 ring-emerald-500/20">
                            <img src="{{ asset('img/logo.png') }}" alt="Logo" class="w-14 h-14 object-contain">
                        </div>
                        <div>
                            <div class="flex items-center gap-3">
                                <h1 class="text-6xl font-black text-white tracking-tight" style="font-family: 'Outfit', sans-serif;">
                                    PRESENCE BOARD
                                </h1>
                                <div class="flex items-center gap-2 px-3 py-1 bg-emerald-500/20 rounded-full border border-emerald-500/30">
                                    <span class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></span>
                                    <span class="text-emerald-300 text-sm font-semibold tracking-wider">LIVE</span>
                                </div>
                            </div>
                            <p class="text-slate-400 text-xl font-medium tracking-wide mt-1">
                                Real-Time Employee Status Monitor
                            </p>
                        </div>
                    </div>

                    {{-- Right: Clock --}}
                    <div class="text-right"
                         x-data="{ now: new Date() }"
                         x-init="setInterval(() => { now = new Date() }, 1000)">
                        <div class="text-6xl font-bold text-emerald-400 tabular-nums tracking-tight"
                             style="font-family: 'JetBrains Mono', monospace;"
                             x-text="now.toLocaleTimeString('en-GB', {hour:'2-digit',minute:'2-digit',second:'2-digit'})">
                        </div>
                        <div class="text-slate-400 text-lg font-medium tracking-wide mt-1"
                             x-text="now.toLocaleDateString('en-GB', {weekday: 'long', day:'2-digit', month:'short', year:'numeric'})">
                        </div>
                    </div>

                </div>

                {{-- Stats Bar --}}
                <div class="mt-6 flex items-center gap-6 text-sm font-semibold">
                    <div class="flex items-center gap-2 px-4 py-2 bg-emerald-500/10 rounded-lg border border-emerald-500/20">
                        <span class="text-emerald-400">👥</span>
                        <span class="text-white">{{ $this->presentCount }}</span>
                        <span class="text-slate-400">Present</span>
                    </div>
                    <div class="flex items-center gap-2 px-4 py-2 bg-rose-500/10 rounded-lg border border-rose-500/20">
                        <span class="text-rose-400">🏖️</span>
                        <span class="text-white">{{ $this->awayCount }}</span>
                        <span class="text-slate-400">Away</span>
                    </div>
                    <div class="flex items-center gap-2 px-4 py-2 bg-amber-500/10 rounded-lg border border-amber-500/20">
                        <span class="text-amber-400">📋</span>
                        <span class="text-white">{{ $this->totalUsers }}</span>
                        <span class="text-slate-400">Total</span>
                    </div>
                    <div class="flex-1"></div>
                    <div class="text-slate-500 text-xs">
                        Last updated: <span class="text-slate-400 font-mono">{{ now()->format('H:i:s') }}</span>
                    </div>
                </div>
            </div>
        </header>

        {{-- Main Content: Employee Cards Grid --}}
        <main class="flex-1 overflow-y-auto px-8 py-8">
            <div class="grid grid-cols-1 
             min-[1440px]:grid-cols-3   {{-- START 3 COLUMNS at 1440px --}}
             min-[1920px]:grid-cols-4   {{-- START 4 COLUMNS at 1920px (8 cards = 2 rows) --}}
             min-[2560px]:grid-cols-5   {{-- START 5 COLUMNS at 2560px (8 cards = 2 rows + 3 empty) --}}
             gap-6">
                
                @forelse($this->users as $user)
                    @php
                        $movement = $user->currentMovement;
                        $cardData = $this->getCardData($user, $movement);
                    @endphp

                    <div class="employee-card group" 
                         wire:key="employee-{{ $user->id }}"
                         style="border-left-color: {{ $cardData['borderColor'] }}">
                        
                        {{-- Card Header: Photo + Status Badge --}}
                        <div class="flex items-start justify-between mb-4">
                            <img src="{{ $user->profile_photo_url }}" 
                                 alt="{{ $user->name }}"
                                 class="w-24 h-24 rounded-xl border-4 object-cover shadow-lg"
                                 style="border-color: {{ $cardData['borderColor'] }}">
                            
                            <div class="px-3 py-1 rounded-full text-xs font-bold tracking-wider shadow-lg"
                                 style="background-color: {{ $cardData['badgeBg'] }}; color: {{ $cardData['badgeText'] }}">
                                {{ $cardData['badgeIcon'] }}
                            </div>
                        </div>

                        {{-- Employee Info --}}
                        <div class="space-y-2">
                            <h3 class="text-2xl font-bold text-white leading-tight" style="font-family: 'Inter', sans-serif;">
                                {{ $user->name }}
                            </h3>
                            <p class="text-slate-400 text-base font-medium">
                                {{ $user->department }}
                            </p>
                            <p class="text-slate-500 text-sm">
                                {{ $user->position }}
                            </p>
                        </div>

                        {{-- Status Display --}}
                        <div class="mt-4 pt-4 border-t border-slate-700/50">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="text-lg font-bold tracking-wide"
                                         style="color: {{ $cardData['statusColor'] }}">
                                        {{ $cardData['statusIcon'] }} {{ $cardData['typeLabel'] }}
                                    </div>
                                    @if($movement && $movement->ended_at)
                                        <div class="text-sm text-slate-400 mt-1 font-mono">
                                            Returns: {{ $movement->ended_at->isToday() ? $movement->ended_at->format('H:i') : $movement->ended_at->format('d M Y') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                    </div>

                @empty
                    <div class="col-span-full flex flex-col items-center justify-center py-20">
                        <div class="text-6xl mb-4">👥</div>
                        <div class="text-2xl font-bold text-slate-400">No Employee Records Found</div>
                        <div class="text-slate-500 mt-2">Please check back later</div>
                    </div>
                @endforelse

            </div>
        </main>

        {{-- Footer --}}
        <footer class="bg-slate-900 border-t-2 border-emerald-500/30 px-8 py-4">
            <div class="flex items-center justify-between text-sm">
                <div class="flex items-center gap-4">
                    <span class="text-slate-400 font-semibold tracking-wide">
                        Presence Board <span class="text-emerald-400">v1.0</span>
                    </span>
                    <span class="text-slate-600">•</span>
                    <span class="text-slate-500 text-xs">© {{ date('Y') }} {{ config('app.name') }}</span>
                </div>

                <div class="flex items-center gap-4">
                    <span class="text-slate-500 font-mono">
                        Page {{ $page }} of {{ $this->totalPages() }}
                    </span>
                    <span class="text-slate-600">•</span>
                    <div class="flex items-center gap-2 text-slate-500 text-xs">
                        <div class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></div>
                        Auto-refresh
                    </div>
                </div>
            </div>
        </footer>

    </div>

    {{-- Screen Size Warning --}}
    <div class="flex min-[1280px]:hidden w-full h-screen items-center justify-center bg-slate-950 px-8">
        <div class="text-center">
            <div class="text-6xl mb-6">📺</div>
            <h2 class="text-3xl font-bold text-rose-400 mb-4">Display Not Supported</h2>
            <p class="text-xl text-slate-400 mb-2">Minimum screen width required: 1280px</p> <p class="text-sm text-slate-500">Please use a larger display for optimal viewing</p>
        </div>
    </div>

</div>

@push('styles')
<style>
    /* Import Modern Fonts */
    @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800;900&family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap');

    /* Employee Card Styling */
    .employee-card {
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        border-radius: 1rem;
        padding: 1.5rem;
        border-left: 5px solid;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.3), 0 2px 4px -1px rgba(0, 0, 0, 0.2);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        animation: fadeInUp 0.5s ease-out;
    }

    .employee-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.4), 0 4px 6px -2px rgba(0, 0, 0, 0.3);
    }

    /* Fade In Animation */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Smooth Scrollbar */
    main::-webkit-scrollbar {
        width: 8px;
    }

    main::-webkit-scrollbar-track {
        background: #0f172a;
    }

    main::-webkit-scrollbar-thumb {
        background: #334155;
        border-radius: 4px;
    }

    main::-webkit-scrollbar-thumb:hover {
        background: #475569;
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
</style>
@endpush
