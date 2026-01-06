<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Clement Dorem">
    <title>{{ $title ?? config('app.name', 'Presence Board') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans antialiased bg-black text-white overflow-hidden">

    {{-- 1. Main content container (Visible only on medium/desktop size and above) --}}
    <div class="hidden md:block h-screen w-full">
        {{ $slot }}
    </div>

    {{-- 2. Not Supported message (Visible only on small sizes) --}}
    <div class="md:hidden flex items-center justify-center h-screen text-center p-8 bg-gray-900">
        <div class="text-red-400">
            <h1 class="text-4xl font-bold mb-4">Display Not Supported</h1>
            <p class="text-xl">
                This screen is intended for large public displays (monitors/TVs) only.<br>
                Please view on a larger screen.
            </p>
        </div>
    </div>
    
    @livewireScripts
</body>
</html>
