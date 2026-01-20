<div wire:poll.8s="rotatePage" class="h-screen w-screen bg-black flex flex-col overflow-hidden font-sans border-[10px] border-black">

    {{-- Header: Brand & Identity --}}
    <header class="h-[14%] border-b-2 border-slate-900 flex items-center justify-between px-8 mb-4 bg-black">
        <div class="flex items-center gap-10">
            {{-- Brand Logo --}}
            <div class="shrink-0">
                @if(file_exists(public_path('img/logo.png')))
                    <img src="{{ asset('img/logo.png') }}" alt="Logo" class="h-20 w-auto object-contain filter brightness-110">
                @else
                    <div class="w-16 h-16 border-2 border-blue-600 flex items-center justify-center">
                        <i data-lucide="building-2" class="w-10 h-10 text-blue-600"></i>
                    </div>
                @endif
            </div>
            
            <div class="h-16 w-[2px] bg-slate-800"></div> {{-- Vertical Separator --}}
            
            <div>
                <h1 class="text-5xl font-black text-white tracking-tighter uppercase leading-none">Personnel Status</h1>
                <p class="text-slate-500 font-mono text-[11px] tracking-[0.5em] uppercase mt-2 italic">Automated Distribution System</p>
            </div>
        </div>

        {{-- Clock: Oversized for visibility --}}
        <div class="text-right font-mono" x-data="{ now: new Date() }" x-init="setInterval(() => now = new Date(), 1000)">
            <div class="text-7xl font-black text-white tracking-tighter leading-none tabular-nums" 
                 x-text="now.toLocaleTimeString('en-GB', {hour:'2-digit', minute:'2-digit', second:'2-digit'})"></div>
            <div class="text-blue-600 text-[12px] font-bold uppercase mt-2 tracking-[0.2em]" 
                 x-text="now.toLocaleDateString('en-GB', {weekday:'long', day:'2-digit', month:'long'})"></div>
        </div>
    </header>

    {{-- Main Grid: 4x2 Layout --}}
    <main class="flex-1 px-4">
        <div class="grid grid-cols-4 grid-rows-2 h-full w-full gap-4">
            @forelse($this->users as $user)
                @php 
                    $card = $this->getCardData($user); 
                    $movement = $user->current_movement;
                @endphp

                <div class="bg-[#080808] border border-slate-900 flex flex-col relative overflow-hidden shadow-[0_0_15px_rgba(0,0,0,0.5)]">
                    
                    {{-- Status Banner --}}
                    <div class="h-2 w-full" style="background-color: {{ $card['statusColor'] }}"></div>

                    <div class="flex-1 flex flex-col p-6">
                        <div class="flex gap-6 items-start h-full">
                            {{-- Profile Photo --}}
                            <div class="shrink-0 border border-slate-800 p-1 bg-black">
                                <img src="{{ $user->profile_photo_url }}" 
                                     class="w-24 h-24 object-cover filter grayscale">
                            </div>

                            {{-- Info --}}
                            <div class="flex-1 min-w-0">
                                <h3 class="text-3xl font-black text-white uppercase leading-[0.85] tracking-tighter break-words mb-3">
                                    {{ $user->name }}
                                </h3>
                                <div class="space-y-1">
                                    <p class="text-blue-500 font-bold text-[10px] uppercase tracking-[0.2em] truncate">{{ $user->department }}</p>
                                    <p class="text-slate-500 text-[10px] font-bold truncate uppercase tracking-tighter">{{ $user->position }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- Card Footer --}}
                        <div class="mt-auto pt-4 border-t border-slate-900/50 flex justify-between items-end">
                            <div class="flex flex-col">
                                <span class="text-[9px] text-slate-600 font-bold uppercase tracking-widest mb-1 leading-none">Location Status</span>
                                <span class="text-xl font-black uppercase tracking-tight leading-none" style="color: {{ $card['statusColor'] }}">
                                    {{ $card['typeLabel'] }}
                                </span>
                            </div>
                            
                            {{-- @if($movement?->ended_at)
                                <div class="text-right bg-slate-900/30 p-2">
                                    <span class="block text-[8px] text-slate-600 font-bold uppercase mb-1 leading-none tracking-widest">Return Date / Time</span>
                                    <span class="text-lg font-mono font-bold text-slate-300">{{ $movement->ended_at->format('d.m.Y H:i') }}</span>
                                </div>
                            @endif --}}

                            @if($movement?->ended_at)
                                <div class="text-right bg-slate-900/40 p-3 border-l-2 border-slate-800">
                                    <span class="block text-[9px] text-slate-500 font-black uppercase mb-1 leading-none tracking-[0.2em]">
                                        @if($movement->ended_at->isToday())
                                            Expected Today
                                        @elseif($movement->ended_at->isTomorrow())
                                            Expected Tomorrow
                                        @else
                                            Expected Return
                                        @endif
                                    </span>
                                    
                                    <span class="text-2xl font-mono font-black text-slate-200 leading-none tabular-nums">
                                        @if($movement->ended_at->isToday())
                                            {{ $movement->ended_at->format('H:i') }}
                                        @else
                                            {{ $movement->ended_at->format('d M') }} <span class="text-sm text-slate-500">{{ $movement->ended_at->format('H:i') }}</span>
                                        @endif
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-4 row-span-2 flex items-center justify-center">
                    <p class="text-slate-800 text-5xl font-black uppercase tracking-[0.5em]">Syncing Personnel...</p>
                </div>
            @endforelse
        </div>
    </main>

    {{-- Footer: Pagination Focused --}}
    <footer class="h-[10%] border-t-2 border-slate-900 flex items-center justify-between px-10 bg-black mt-2">
        
        {{-- Status Counts (Switched to Left) --}}
        <div class="flex gap-8">
            <div class="flex flex-col">
                <span class="text-slate-600 text-[9px] font-bold uppercase tracking-widest">Present</span>
                <span class="text-2xl font-black text-emerald-500 leading-none">{{ $this->presentCount }}</span>
            </div>
            <div class="flex flex-col">
                <span class="text-slate-600 text-[9px] font-bold uppercase tracking-widest">Away</span>
                <span class="text-2xl font-black text-amber-500 leading-none">{{ $this->awayCount }}</span>
            </div>
        </div>

        {{-- Centered Pagination --}}
        <div class="flex flex-col items-center">
            <span class="text-slate-500 text-[10px] font-bold uppercase tracking-[0.4em] mb-2 leading-none">Rotation Index</span>
            <div class="flex gap-2 items-center">
                @for($i = 1; $i <= $this->totalPages; $i++)
                    <div class="h-2 w-12 {{ $i == $page ? 'bg-blue-600 shadow-[0_0_10px_rgba(37,99,235,0.8)]' : 'bg-slate-800' }} transition-all duration-500"></div>
                @endfor
            </div>
            <span class="text-white font-mono text-sm font-black mt-2 uppercase tracking-widest">
                Page {{ $page }} / {{ $this->totalPages }}
            </span>
        </div>

        {{-- System Health (Right) --}}
        <div class="text-right">
            <div class="flex items-center justify-end gap-2 mb-1">
                <span class="text-slate-600 text-[9px] font-bold uppercase tracking-widest">System Signal</span>
                <div class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></div>
            </div>
            <span class="text-slate-700 font-mono text-[9px] font-bold uppercase">
                Last Sync: {{ now()->format('H:i:s') }}
            </span>
        </div>
    </footer>
</div>

@push('styles')
<style>
    /* Strictly View-Only Settings */
    * {
        border-radius: 0 !important;
        cursor: none !important;
        user-select: none !important;
        pointer-events: none !important;
    }

    body {
        background-color: black;
    }

    /* Grayscale ensures the status colors (green/amber) are the only visual priorities */
    img {
        filter: grayscale(100%) contrast(1.1);
    }
</style>
@endpush
