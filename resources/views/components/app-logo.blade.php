{{-- <div class="flex aspect-square size-8 items-center justify-center rounded-md bg-accent-content text-accent-foreground">
    <x-app-logo-icon class="size-5 fill-current text-white dark:text-black" />
</div>
<div class="ms-1 grid flex-1 text-start text-sm">
    <span class="mb-0.5 truncate leading-tight font-semibold">Laravel Starter Kit</span>
</div> --}}

<div class="flex items-center">
    {{-- Removed the background box and the 'size-8' constraint --}}
    <div class="flex items-center justify-center">
        {{-- 
            Using h-7 to keep it aligned with the text height. 
            'w-auto' ensures your horizontal logo isn't squashed.
        --}}
        <x-app-logo-icon class="h-7 w-auto" />
    </div>
    
    <div class="ms-3 grid flex-1 text-start text-sm">
        <span class="mb-0.5 truncate leading-tight font-bold dark:text-white text-neutral-900">
            {{ config('app.name') }}
        </span>
    </div>
</div>
