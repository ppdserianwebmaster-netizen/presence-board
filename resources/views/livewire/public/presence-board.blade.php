<div wire:poll.8s="rotatePage" class="h-full w-full p-6 flex flex-col bg-gray-900">

    @php use App\Models\Movement; @endphp

    {{-- Header --}}
    <div class="flex justify-between items-center mb-6 border-b-2 border-yellow-600 pb-2">
        <div class="flex items-center">
            <img src="{{ asset('img/logo.png') }}" alt="Presence Board Logo" class="h-14 w-auto mr-6">
            <h1 class="text-5xl font-mono text-yellow-500 uppercase tracking-widest truncate">PRESENCE BOARD</h1>
        </div>

        {{-- Clock --}}
        <div x-data="{ now: new Date() }"
             x-init="setInterval(() => { now = new Date() }, 1000)"
             class="text-3xl text-yellow-300 font-mono text-right">
            <span x-text="now.toLocaleTimeString('en-GB', {hour:'2-digit',minute:'2-digit',second:'2-digit'})"></span>
            <div class="text-base text-gray-400 uppercase tracking-wide mt-1">
                <span x-text="now.toLocaleDateString('en-GB', {day:'2-digit',month:'short',year:'numeric'})"></span>
            </div>
        </div>
    </div>

    {{-- Cards Grid (The combination for guaranteed 5x2 stability) --}}
    {{-- Removed overflow-hidden from here, relying on min-h-0 to control growth and the h-full parent --}}
    <div class="flex-grow grid grid-cols-5 gap-4 grid-rows-2 min-h-0">

        @php
            $cardCount = $users->count();
            // Use the component property, assuming it's correctly passed/available.
            $perPage = $this->perPage ?? 10; 
            $emptyCards = max(0, $perPage - $cardCount);
        @endphp

        @forelse($users as $user)
            @php
                // Data Logic remains the same (The logic is sound)
                $movement = $user->current_movement;
                $statusText = $movement ? 'UNAVAILABLE' : 'AVAILABLE';
                $statusClass = $movement ? 'bg-red-600 text-white animate-pulse' : 'bg-green-600 text-white';
                $borderClass = $movement ? 'border-red-500' : 'border-green-500';

                $returnTime = null;
                $typeLabel = null;
                $returnClass = 'text-yellow-300';

                if($movement) {
                    $end = $movement->end_datetime;
                    $typeLabel = Movement::TYPES[$movement->type] ?? $movement->type;

                    if ($end) {
                        if ($end->isToday()) {
                            $returnTime = "Back at ".$end->format('H:i');
                            $returnClass = 'text-yellow-300';
                        } elseif ($end->isTomorrow()) {
                            $returnTime = "Return: Tomorrow";
                            $returnClass = 'text-orange-400';
                        } else {
                            $returnTime = "Return: ".$end->format('d M Y'); 
                            $returnClass = 'text-orange-400';
                        }
                    } else {
                         $returnTime = "Return: TBD/Ongoing";
                         $returnClass = 'text-red-300';
                    }
                }
            @endphp

            {{-- Card (If the previous fix failed, try reducing padding to p-4) --}}
            <div class="bg-gray-800 rounded-lg p-5 flex flex-col justify-between shadow-lg"> 
                <div class="flex flex-col flex-grow justify-between items-center text-center">

                    {{-- Employee Info Block --}}
                    <div class="flex flex-col items-center text-center w-full"> 
                        {{-- Profile Photo --}}
                        <img
                            src="{{ $user->profile_photo_url }}"
                            alt="{{ $user->name }}"
                            class="w-20 h-20 object-cover rounded-md border-4 {{ $borderClass }} mb-3"
                        >

                        {{-- Employee Name --}}
                        <div
                            class="text-2xl font-bold text-white w-full truncate"
                            title="{{ $user->name }}"
                        >
                            {{ $user->name }}
                        </div>

                        {{-- Position --}}
                        <div
                            class="text-lg text-gray-400 uppercase tracking-wide w-full truncate"
                            title="{{ $user->position }}"
                        >
                            {{ $user->position }}
                        </div>

                        {{-- Department --}}
                        <div
                            class="text-lg text-gray-400 w-full truncate"
                            title="{{ $user->department }}"
                        >
                            {{ $user->department }}
                        </div>
                    </div>

                    {{-- Status Badge --}}
                    <div class="mt-4 w-full">
                        <span class="inline-block px-4 py-2 rounded font-bold shadow-md text-lg {{ $statusClass }} w-full">
                            {{ $statusText }}
                        </span>
                    </div>

                    {{-- Movement Info --}}
                    <div class="mt-3 text-gray-300 w-full">
                        @if($movement)
                            <div class="{{ $returnClass }} font-semibold text-lg">{{ $returnTime }}</div>
                            <div class="uppercase tracking-wide text-lg">{{ $typeLabel }}</div>
                        @else
                            {{-- Placeholders to balance card height --}}
                            <div class="text-green-400 font-semibold text-lg">&nbsp;</div>
                            <div class="text-green-400 font-semibold text-lg">Available in Office</div>
                        @endif
                    </div>

                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-20 text-gray-600">
                <p class="text-3xl">No employee records found.</p>
                <p>The board will rotate soon.</p>
            </div>
        @endforelse

        {{-- Placeholder Cards to stabilize the grid on the last page --}}
        @for ($i = 0; $i < $emptyCards; $i++)
            <div class="invisible bg-gray-800 rounded-lg p-5 shadow-lg"></div>
        @endfor

    </div>

    {{-- Footer --}}
    <div class="mt-4 flex justify-between text-gray-600 font-mono text-base">
        <span>Server Time: {{ now()->format('H:i:s') }} . Presence Board v.1 Pre-Alpha 1</span> 
        <span wire:key="pagination-info-{{ $this->page }}">Page {{ $this->page }} of {{ $this->totalPages }}</span>
    </div>
</div>
