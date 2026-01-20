<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>{{ $title ?? 'Presence Board' }}</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&family=JetBrains+Mono:wght@700&display=swap" rel="stylesheet">
    
    <script src="https://unpkg.com/lucide@latest"></script>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    
    <style>
        :root { --surface: #020617; --card: #0f172a; --accent: #2563eb; }
        * { border-radius: 0 !important; cursor: none !important; } /* Corporate Square Look */
        body { background: var(--surface); color: white; font-family: 'Inter', sans-serif; overflow: hidden; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="antialiased select-none">
    <main>{{ $slot }}</main>
    @livewireScripts
    <script>
        document.addEventListener('livewire:init', () => {
            const refresh = () => window.lucide && lucide.createIcons();
            Livewire.hook('morph.updated', refresh);
            refresh();
        });
    </script>
</body>
</html>
