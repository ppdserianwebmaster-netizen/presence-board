{{-- resources\views\components\layouts\public.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>{{ $title ?? 'Presence Board' }}</title>
    
    {{-- High-Performance Font Loading --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&family=JetBrains+Mono:wght@700&display=swap" rel="stylesheet">
    
    {{-- Lucide Icons (CDN) --}}
    <script src="https://unpkg.com/lucide@latest" defer></script>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    
    <style>
        :root { 
            --surface: #000000; {{-- Switched to pure black for OLED/Dashboard screens --}}
            --card: #080808; 
            --accent: #2563eb; 
        }
        
        {{-- Strictly View-Only Enforcement --}}
        * { 
            border-radius: 0 !important; 
            cursor: none !important; 
            user-select: none !important;
            -webkit-user-drag: none;
        }

        body { 
            background: var(--surface); 
            color: white; 
            font-family: 'Inter', sans-serif; 
            overflow: hidden; 
            height: 100vh;
            width: 100vw;
        }

        [x-cloak] { display: none !important; }

        {{-- Smooth transitions for Livewire Morph updates --}}
        .livewire-morph-fade {
            transition: opacity 0.3s ease-in-out;
        }
    </style>
</head>
<body class="antialiased select-none">
    <main class="h-full w-full">
        {{ $slot }}
    </main>

    @livewireScripts
    
    <script>
        /**
         * Re-initialize Lucide icons after Livewire updates the DOM.
         */
        document.addEventListener('livewire:init', () => {
            const refreshIcons = () => {
                if (window.lucide) {
                    lucide.createIcons();
                }
            };

            // Hook into Livewire's lifecycle to catch DOM changes
            Livewire.hook('morph.updated', (el, component) => {
                refreshIcons();
            });

            // Initial run
            refreshIcons();
        });

        /**
         * Optional: Auto-reload the entire page once every 24 hours 
         * to clear browser memory leaks (common on TV browsers).
         */
        setTimeout(() => {
            window.location.reload();
        }, 86400000); 
    </script>
    @stack('scripts')
</body>
</html>
