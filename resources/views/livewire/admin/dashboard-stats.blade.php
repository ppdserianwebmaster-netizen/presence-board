{{-- resources/views/livewire/admin/dashboard-stats.blade.php --}}
<div class="p-6">
    {{-- Header Section: Aligned with Index pages --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <div>
            <h1 class="text-2xl font-bold dark:text-white tracking-tight">Admin Overview</h1>
            <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-neutral-500 mt-1">
                Live system status <span class="mx-1 text-neutral-300">•</span> {{ now()->format('D, d M Y') }}
            </p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.users.index') }}" wire:navigate 
               class="px-5 py-2.5 text-[10px] font-bold uppercase tracking-widest bg-white dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl shadow-sm hover:bg-neutral-50 dark:hover:bg-neutral-700 transition-all dark:text-white">
                Manage Users
            </a>
            <a href="{{ route('admin.movements.index') }}" wire:navigate 
               class="px-5 py-2.5 text-[10px] font-bold uppercase tracking-widest bg-neutral-900 dark:bg-white text-white dark:text-black rounded-xl shadow-sm hover:opacity-90 transition-all">
                All Logs
            </a>
        </div>
    </div>

    {{-- Statistics Grid: Refined rounded corners and border logic --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
        {{-- Total Staff Card --}}
        <div class="p-5 bg-neutral-900 dark:bg-white rounded-2xl shadow-lg">
            <div class="text-[10px] font-bold uppercase text-neutral-400 dark:text-neutral-500 tracking-widest mb-1">Total Staff</div>
            <div class="text-3xl font-bold text-white dark:text-black leading-none">{{ $totalEmployees }}</div>
        </div>

        {{-- Dynamic Type Cards --}}
        @foreach($typeCounts as $stat)
            <div class="p-5 bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-800 rounded-2xl shadow-sm transition-all group">
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-2 h-2 rounded-full bg-{{ $stat['color'] }}-500 {{ $stat['count'] > 0 ? 'animate-pulse' : 'opacity-30' }}"></div>
                    <div class="text-[10px] font-bold uppercase text-neutral-500 tracking-tight">
                        {{ $stat['label'] }}
                    </div>
                </div>
                <div class="text-3xl font-bold dark:text-white leading-none">
                    {{ $stat['count'] }}
                </div>
            </div>
        @endforeach
    </div>

    {{-- Activity Feed: Synced with Movement Table Styling --}}
    <div class="grid grid-cols-1 gap-6">
        <div class="bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-800 rounded-2xl overflow-hidden shadow-sm">
            <div class="px-6 py-4 border-b border-neutral-100 dark:border-neutral-800 flex justify-between items-center bg-neutral-50/50 dark:bg-neutral-800/30">
                <span class="text-[10px] font-bold uppercase tracking-[0.2em] dark:text-white">Recent Activity Feed</span>
                <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-tighter bg-indigo-50 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-800">
                    {{ $totalOutNow }} Personnel Out
                </span>
            </div>
            
            <div class="divide-y divide-neutral-100 dark:divide-neutral-800">
                @forelse($recentActivity as $activity)
                    <div wire:key="activity-{{ $activity->id }}" class="px-6 py-4 flex justify-between items-center hover:bg-neutral-50 dark:hover:bg-neutral-800/50 transition-colors">
                        <div class="flex items-center gap-4">
                            {{-- Avatar Style: Rounded-xl to match other cards --}}
                            <div class="h-10 w-10 rounded-xl border border-{{ $activity->type->color() }}-200 dark:border-{{ $activity->type->color() }}-800 bg-{{ $activity->type->color() }}-50 dark:bg-{{ $activity->type->color() }}-900/20 flex items-center justify-center font-bold text-xs text-{{ $activity->type->color() }}-700 dark:text-{{ $activity->type->color() }}-400">
                                {{ $activity->user->initials() }}
                            </div>
                            <div>
                                <div class="text-sm font-bold text-neutral-900 dark:text-white">{{ $activity->user->name }}</div>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <span class="text-[10px] font-bold uppercase text-{{ $activity->type->color() }}-600 dark:text-{{ $activity->type->color() }}-400">
                                        {{ $activity->type->label() }}
                                    </span> 
                                    <span class="text-[10px] font-medium text-neutral-400">
                                        • {{ $activity->started_at->diffForHumans() }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('admin.movements.index', ['search' => $activity->user->name]) }}" wire:navigate 
                           class="px-4 py-2 text-[10px] font-bold uppercase tracking-widest border border-neutral-200 dark:border-neutral-700 rounded-lg text-neutral-500 hover:text-neutral-900 dark:hover:text-white hover:bg-neutral-50 dark:hover:bg-neutral-800 transition-all">
                            View Log
                        </a>
                    </div>
                @empty
                    <div class="px-8 py-12 text-center text-neutral-400 italic text-sm">
                        No recent activity recorded today.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
