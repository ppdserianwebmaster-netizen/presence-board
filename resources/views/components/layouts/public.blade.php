{{-- resources/views/components/layouts/public.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    
    <title>Presence Board v2.0 - Employee Status Monitor</title>
    
    {{-- SEO & Security --}}
    <meta name="description" content="Real-time employee presence and status monitoring board">
    <meta name="robots" content="noindex, nofollow">
    <meta name="author" content="Clement Dorem">
    
    {{-- Auto-refresh fallback (every 5 minutes) --}}
    <meta http-equiv="refresh" content="300">
    
    {{-- Favicon --}}
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    
    {{-- Preconnect to Google Fonts for faster loading --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    {{-- Modern Corporate Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Space+Grotesk:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    
    {{-- Lucide Icons CDN --}}
    <script src="https://unpkg.com/lucide@latest"></script>
    
    {{-- Vite Assets --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    {{-- Livewire Styles --}}
    @livewireStyles
    
    {{-- Additional styles from pages --}}
    @stack('styles')
    
    <style>
        /* Dark Corporate Color Palette */
        :root {
            --corporate-primary: #3B82F6;
            --corporate-primary-light: #60A5FA;
            --corporate-secondary: #8B5CF6;
            --corporate-success: #10B981;
            --corporate-warning: #F59E0B;
            --corporate-danger: #EF4444;
            --corporate-info: #06B6D4;
            
            --surface-bg: #0F172A;
            --surface-card: #1E293B;
            --surface-elevated: #334155;
            
            --text-primary: #F1F5F9;
            --text-secondary: #CBD5E1;
            --text-tertiary: #64748B;
            
            --border-subtle: #334155;
            --border-medium: #475569;
            
            --glow-primary: rgba(59, 130, 246, 0.3);
            --glow-success: rgba(16, 185, 129, 0.3);
        }
        
        /* Prevent text selection for display board */
        body {
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
            cursor: default;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--surface-bg);
        }
        
        /* Hide cursor after inactivity */
        body.hide-cursor {
            cursor: none;
        }
        
        /* Smooth font rendering */
        * {
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 10px;
        }
        
        ::-webkit-scrollbar-track {
            background: var(--surface-bg);
        }
        
        ::-webkit-scrollbar-thumb {
            background: var(--border-medium);
            border-radius: 5px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: var(--surface-elevated);
        }
    </style>
</head>

<body class="bg-slate-950 antialiased overflow-hidden">
    
    {{-- Main Content Slot --}}
    {{ $slot }}
    
    {{-- Livewire Scripts --}}
    @livewireScripts
    
    {{-- Initialize Lucide Icons --}}
    <script>
        lucide.createIcons();
    </script>
    
    {{-- Additional scripts from pages --}}
    @stack('scripts')
    
    {{-- Enhanced Monitoring & Auto-refresh Script --}}
    <script>
        // Configuration
        const CONFIG = {
            inactivityThreshold: 60,
            cursorHideDelay: 10,
            debugMode: false
        };

        let inactivityTime = 0;
        let cursorTimeout = null;

        function log(message, type = 'info') {
            if (!CONFIG.debugMode) return;
            const timestamp = new Date().toLocaleTimeString();
            console[type](`[${timestamp}] Presence Board:`, message);
        }

        function resetCursorTimeout() {
            document.body.classList.remove('hide-cursor');
            clearTimeout(cursorTimeout);
            
            cursorTimeout = setTimeout(() => {
                document.body.classList.add('hide-cursor');
                log('Cursor hidden due to inactivity');
            }, CONFIG.cursorHideDelay * 1000);
        }

        function resetInactivity() {
            inactivityTime = 0;
            resetCursorTimeout();
            log('Activity detected, timer reset');
        }

        ['mousemove', 'keypress', 'touchstart', 'click'].forEach(event => {
            document.addEventListener(event, resetInactivity, { passive: true });
        });

        resetCursorTimeout();

        setInterval(() => {
            inactivityTime++;
            log(`Inactivity time: ${inactivityTime} minutes`);
            
            if (inactivityTime >= CONFIG.inactivityThreshold) {
                log('Reloading due to prolonged inactivity', 'warn');
                window.location.reload();
            }
        }, 60000);

        document.addEventListener('livewire:init', () => {
            log('Livewire initialized successfully');
        });

        document.addEventListener('visibilitychange', () => {
            if (!document.hidden) {
                log('Tab visible again, resetting inactivity');
                resetInactivity();
            }
        });

        window.addEventListener('load', () => {
            log('Page fully loaded');
            lucide.createIcons();
        });

        window.addEventListener('error', (event) => {
            log(`Error detected: ${event.message}`, 'error');
        });

        if ('serviceWorker' in navigator && 'onLine' in navigator) {
            window.addEventListener('offline', () => {
                log('Connection lost - offline', 'warn');
            });
            
            window.addEventListener('online', () => {
                log('Connection restored - online');
            });
        }

        document.addEventListener('gesturestart', (e) => {
            e.preventDefault();
        });

        document.addEventListener('touchmove', (e) => {
            if (e.scale !== 1) {
                e.preventDefault();
            }
        }, { passive: false });

        log('Presence Board v2.0 initialized');
        log(`Auto-refresh after ${CONFIG.inactivityThreshold} minutes of inactivity`);
    </script>
    
</body>
</html>
