<div class="p-6">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-2xl font-bold dark:text-white">Admin Overview</h1>
            <p class="text-sm text-neutral-500 font-medium uppercase tracking-widest text-[10px]">
                Live system status for {{ now()->format('D, d M Y') }}
            </p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.users.index') }}" wire:navigate class="px-4 py-2 text-xs font-bold bg-white dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg shadow-sm hover:bg-neutral-50 dark:hover:bg-neutral-700 transition-all dark:text-white">
                Manage Users
            </a>
            <a href="{{ route('admin.movements.index') }}" wire:navigate class="px-4 py-2 text-xs font-bold bg-neutral-900 dark:bg-white text-white dark:text-black rounded-lg shadow-sm hover:opacity-90 transition-all">
                All Logs
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-6 gap-4 mb-8">
        <div class="p-4 bg-neutral-900 dark:bg-white rounded-2xl shadow-lg">
            <div class="text-[10px] font-black uppercase text-neutral-400 dark:text-neutral-500 tracking-tighter">Total Staff</div>
            <div class="text-3xl font-black text-white dark:text-black">{{ $totalEmployees }}</div>
        </div>

        @foreach($typeCounts as $stat)
            {{-- $stat['color'] should return 'blue', 'indigo', etc. from your Enum --}}
            <div class="p-4 bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-800 rounded-2xl shadow-sm hover:border-{{ $stat['color'] }}-300 transition-colors">
                <div class="text-[10px] font-bold uppercase text-{{ $stat['color'] }}-600 dark:text-{{ $stat['color'] }}-400 mb-1 tracking-tight">
                    {{ $stat['label'] }}
                </div>
                <div class="text-2xl font-black dark:text-white">
                    {{ $stat['count'] }}
                </div>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 gap-6">
        <div class="bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-800 rounded-2xl overflow-hidden shadow-sm">
            <div class="px-6 py-4 border-b border-neutral-100 dark:border-neutral-800 flex justify-between items-center bg-neutral-50/50 dark:bg-neutral-800/30">
                <span class="text-xs font-black uppercase tracking-widest dark:text-white">Recent Movements</span>
                <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-tighter bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">
                    {{ $totalOutNow }} Active Now
                </span>
            </div>
            
            <div class="divide-y divide-neutral-100 dark:divide-neutral-800">
                @forelse($recentActivity as $activity)
                    <div class="px-6 py-4 flex justify-between items-center hover:bg-neutral-50 dark:hover:bg-neutral-800/50 transition-colors">
                        <div class="flex items-center gap-4">
                            <div class="h-10 w-10 rounded-full border-2 border-{{ $activity->type->color() }}-500/20 bg-{{ $activity->type->color() }}-100 dark:bg-{{ $activity->type->color() }}-900/30 flex items-center justify-center font-bold text-xs text-{{ $activity->type->color() }}-700 dark:text-{{ $activity->type->color() }}-400">
                                {{ $activity->user->initials() }}
                            </div>
                            <div>
                                <div class="text-sm font-bold text-neutral-900 dark:text-white">{{ $activity->user->name }}</div>
                                <div class="text-[10px] text-neutral-500 font-bold uppercase tracking-tighter">
                                    <span class="text-{{ $activity->type->color() }}-600 dark:text-{{ $activity->type->color() }}-400">
                                        {{ $activity->type->label() }}
                                    </span> 
                                    <span class="mx-1 opacity-30">•</span> 
                                    Started {{ $activity->started_at->diffForHumans() }}
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('admin.movements.index', ['search' => $activity->user->name]) }}" wire:navigate class="px-3 py-1 text-[10px] font-black uppercase border border-neutral-200 dark:border-neutral-700 rounded-md text-neutral-400 hover:text-neutral-900 dark:hover:text-white hover:bg-neutral-50 transition-all">
                            View Details
                        </a>
                    </div>
                @empty
                    <div class="px-6 py-10 text-center text-neutral-400 italic text-sm">
                        No activity recorded today.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
