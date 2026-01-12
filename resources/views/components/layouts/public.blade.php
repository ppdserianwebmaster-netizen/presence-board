{{-- resources/views/components/layouts/public.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    
    <title>Presence Board v1.0 - Employee Status Monitor</title>
    
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
    
    {{-- Vite Assets --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    {{-- Livewire Styles --}}
    @livewireStyles
    
    {{-- Additional styles from pages --}}
    @stack('styles')
    
    <style>
        /* Prevent text selection for display board */
        body {
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
            cursor: default;
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
    </style>
</head>

<body class="bg-slate-950 text-white antialiased overflow-hidden">
    
    {{-- Main Content Slot --}}
    {{ $slot }}
    
    {{-- Livewire Scripts --}}
    @livewireScripts
    
    {{-- Additional scripts from pages --}}
    @stack('scripts')
    
    {{-- Enhanced Monitoring & Auto-refresh Script --}}
    <script>
        // Configuration
        const CONFIG = {
            inactivityThreshold: 60, // minutes
            cursorHideDelay: 10, // seconds
            debugMode: false // Set to true for console logging
        };

        // State management
        let inactivityTime = 0;
        let cursorTimeout = null;

        // Logging utility
        function log(message, type = 'info') {
            if (!CONFIG.debugMode) return;
            const timestamp = new Date().toLocaleTimeString();
            console[type](`[${timestamp}] Presence Board:`, message);
        }

        // Hide cursor after inactivity
        function resetCursorTimeout() {
            document.body.classList.remove('hide-cursor');
            clearTimeout(cursorTimeout);
            
            cursorTimeout = setTimeout(() => {
                document.body.classList.add('hide-cursor');
                log('Cursor hidden due to inactivity');
            }, CONFIG.cursorHideDelay * 1000);
        }

        // Reset inactivity timer on any interaction
        function resetInactivity() {
            inactivityTime = 0;
            resetCursorTimeout();
            log('Activity detected, timer reset');
        }

        // Event listeners for activity detection
        ['mousemove', 'keypress', 'touchstart', 'click'].forEach(event => {
            document.addEventListener(event, resetInactivity, { passive: true });
        });

        // Initialize cursor hiding
        resetCursorTimeout();

        // Check inactivity every minute
        setInterval(() => {
            inactivityTime++;
            log(`Inactivity time: ${inactivityTime} minutes`);
            
            // After configured minutes of inactivity, reload page
            if (inactivityTime >= CONFIG.inactivityThreshold) {
                log('Reloading due to prolonged inactivity', 'warn');
                window.location.reload();
            }
        }, 60000); // Every minute

        // Detect if Livewire connection drops
        document.addEventListener('livewire:init', () => {
            log('Livewire initialized successfully');
        });

        // Handle visibility change (tab switching)
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                log('Tab hidden');
            } else {
                log('Tab visible again, resetting inactivity');
                resetInactivity();
            }
        });

        // Performance monitoring
        window.addEventListener('load', () => {
            log('Page fully loaded');
            
            // Optional: Report performance metrics
            if (window.performance && CONFIG.debugMode) {
                const perfData = window.performance.timing;
                const loadTime = perfData.loadEventEnd - perfData.navigationStart;
                log(`Page load time: ${loadTime}ms`);
            }
        });

        // Error handling
        window.addEventListener('error', (event) => {
            log(`Error detected: ${event.message}`, 'error');
        });

        // Service worker for offline detection (optional enhancement)
        if ('serviceWorker' in navigator && 'onLine' in navigator) {
            window.addEventListener('offline', () => {
                log('Connection lost - offline', 'warn');
                // Could show an offline indicator here
            });
            
            window.addEventListener('online', () => {
                log('Connection restored - online');
                // Could hide offline indicator and refresh data
            });
        }

        // Prevent accidental zoom (especially on touch displays)
        document.addEventListener('gesturestart', (e) => {
            e.preventDefault();
        });

        document.addEventListener('touchmove', (e) => {
            if (e.scale !== 1) {
                e.preventDefault();
            }
        }, { passive: false });

        // Log initial load
        log('Presence Board v1.0 initialized');
        log(`Auto-refresh after ${CONFIG.inactivityThreshold} minutes of inactivity`);
    </script>
    
</body>
</html>
