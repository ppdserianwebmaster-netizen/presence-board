<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    
    <title>{{ $title ?? 'Presence Board v2.0' }}</title>
    
    {{-- SEO & Kiosk Metadata --}}
    <meta name="description" content="Real-time employee presence and status monitoring board">
    <meta name="robots" content="noindex, nofollow">
    <meta name="apple-mobile-web-app-capable" content="yes">
    
    {{-- Auto-refresh fallback (5 minutes) --}}
    <meta http-equiv="refresh" content="300">
    
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    
    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">
    
    {{-- Lucide Icons CDN --}}
    <script src="https://unpkg.com/lucide@latest"></script>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @stack('styles')
    
    <style>
        :root {
            --surface-bg: #0F172A;
            --surface-card: #1E293B;
            --border-medium: #475569;
        }
        
        body {
            -webkit-user-select: none;
            user-select: none;
            font-family: 'Inter', sans-serif;
            background-color: var(--surface-bg);
            scrollbar-gutter: stable;
        }

        .hide-cursor { cursor: none !important; }

        /* Custom Scrollbar for Kiosk */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: var(--surface-bg); }
        ::-webkit-scrollbar-thumb { 
            background: var(--border-medium); 
            border-radius: 10px; 
        }

        [x-cloak] { display: none !important; }
    </style>
</head>

<body class="antialiased overflow-hidden text-slate-100">
    
    <main>
        {{ $slot }}
    </main>

    @livewireScripts
    @stack('scripts')

    <script>
        /**
         * Kiosk Mode & Display Logic
         */
        const KIOSK_CONFIG = {
            inactivityReload: 60, // Minutes
            cursorHideDelay: 10,   // Seconds
        };

        let inactivityTimer = 0;
        let cursorTimer;

        // 1. Icon Management (Prevents flickering on Livewire updates)
        const refreshIcons = () => lucide.createIcons();
        window.addEventListener('load', refreshIcons);
        document.addEventListener('livewire:navigated', refreshIcons);
        document.addEventListener('livewire:update', refreshIcons);

        // 2. Cursor Management
        const hideCursor = () => document.body.classList.add('hide-cursor');
        const showCursor = () => {
            document.body.classList.remove('hide-cursor');
            clearTimeout(cursorTimer);
            cursorTimer = setTimeout(hideCursor, KIOSK_CONFIG.cursorHideDelay * 1000);
        };

        // 3. Inactivity Reload
        const resetInactivity = () => {
            inactivityTimer = 0;
            showCursor();
        };

        // Event Listeners
        ['mousemove', 'mousedown', 'keydown', 'touchstart'].forEach(evt => {
            document.addEventListener(evt, resetInactivity, { passive: true });
        });

        // Minute-interval check
        setInterval(() => {
            inactivityTimer++;
            if (inactivityTimer >= KIOSK_CONFIG.inactivityReload) {
                window.location.reload();
            }
        }, 60000);

        // Prevent unwanted touch gestures (Zooming)
        document.addEventListener('gesturestart', e => e.preventDefault());

        showCursor();
        console.log('Board Kiosk Mode: Active');
    </script>
</body>
</html>
