{{-- resources/views/livewire/admin/dashboard-stats.blade.php --}}
<div class="p-6">
    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <div>
            <h1 class="text-2xl font-black dark:text-white tracking-tight">Admin Overview</h1>
            <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-neutral-500 mt-1">
                Live system status <span class="mx-1 text-neutral-300">•</span> {{ now()->format('D, d M Y') }}
            </p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.users.index') }}" wire:navigate 
               class="px-5 py-2.5 text-[10px] font-black uppercase tracking-widest bg-white dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl shadow-sm hover:bg-neutral-50 dark:hover:bg-neutral-700 transition-all dark:text-white">
                Manage Users
            </a>
            <a href="{{ route('admin.movements.index') }}" wire:navigate 
               class="px-5 py-2.5 text-[10px] font-black uppercase tracking-widest bg-neutral-900 dark:bg-white text-white dark:text-black rounded-xl shadow-sm hover:opacity-90 transition-all">
                All Logs
            </a>
        </div>
    </div>

    {{-- Statistics Grid --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
        {{-- Total Staff Card --}}
        <div class="p-5 bg-neutral-900 dark:bg-white rounded-[1.5rem] shadow-xl border border-transparent">
            <div class="text-[10px] font-black uppercase text-neutral-400 dark:text-neutral-500 tracking-widest mb-1">Total Staff</div>
            <div class="text-3xl font-black text-white dark:text-black leading-none">{{ $totalEmployees }}</div>
        </div>

        {{-- Dynamic Type Cards --}}
        @foreach($typeCounts as $stat)
            <div class="p-5 bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-800 rounded-[1.5rem] shadow-sm hover:border-{{ $stat['color'] }}-500/50 transition-all group">
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-2 h-2 rounded-full bg-{{ $stat['color'] }}-500 animate-pulse"></div>
                    <div class="text-[10px] font-black uppercase text-neutral-500 group-hover:text-{{ $stat['color'] }}-600 transition-colors tracking-tight">
                        {{ $stat['label'] }}
                    </div>
                </div>
                <div class="text-3xl font-black dark:text-white leading-none">
                    {{ $stat['count'] }}
                </div>
            </div>
        @endforeach
    </div>

    {{-- Activity Feed --}}
    <div class="grid grid-cols-1 gap-6">
        <div class="bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-800 rounded-[2rem] overflow-hidden shadow-sm">
            <div class="px-8 py-5 border-b border-neutral-100 dark:border-neutral-800 flex justify-between items-center bg-neutral-50/50 dark:bg-neutral-800/30">
                <span class="text-[10px] font-black uppercase tracking-[0.2em] dark:text-white">Recent Activity Feed</span>
                <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-tighter bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400 border border-indigo-200/50 dark:border-indigo-500/20">
                    {{ $totalOutNow }} Personnel Out
                </span>
            </div>
            
            <div class="divide-y divide-neutral-100 dark:divide-neutral-800">
                @forelse($recentActivity as $activity)
                    <div wire:key="activity-{{ $activity->id }}" class="px-8 py-5 flex justify-between items-center hover:bg-neutral-50 dark:hover:bg-neutral-800/50 transition-colors">
                        <div class="flex items-center gap-5">
                            {{-- Avatar Style --}}
                            <div class="h-12 w-12 rounded-2xl border-2 border-{{ $activity->type->color() }}-500/20 bg-{{ $activity->type->color() }}-100 dark:bg-{{ $activity->type->color() }}-900/30 flex items-center justify-center font-black text-xs text-{{ $activity->type->color() }}-700 dark:text-{{ $activity->type->color() }}-400">
                                {{ $activity->user->initials() }}
                            </div>
                            <div>
                                <div class="text-sm font-black text-neutral-900 dark:text-white">{{ $activity->user->name }}</div>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <span class="text-[10px] font-black uppercase px-1.5 py-0.5 rounded bg-neutral-100 dark:bg-neutral-800 text-neutral-500">
                                        {{ $activity->user->employee_id }}
                                    </span>
                                    <span class="text-[10px] font-bold uppercase text-{{ $activity->type->color() }}-600 dark:text-{{ $activity->type->color() }}-400">
                                        {{ $activity->type->label() }}
                                    </span> 
                                    <span class="text-[10px] font-medium text-neutral-400">
                                        — {{ $activity->started_at->diffForHumans() }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('admin.movements.index', ['search' => $activity->user->name]) }}" wire:navigate 
                           class="px-4 py-2 text-[10px] font-black uppercase tracking-widest border border-neutral-200 dark:border-neutral-700 rounded-xl text-neutral-400 hover:text-neutral-900 dark:hover:text-white hover:bg-white dark:hover:bg-neutral-800 shadow-sm transition-all active:scale-95">
                            Log Details
                        </a>
                    </div>
                @empty
                    <div class="px-8 py-16 text-center text-neutral-400 italic text-sm font-medium">
                        No recent activity recorded today.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
