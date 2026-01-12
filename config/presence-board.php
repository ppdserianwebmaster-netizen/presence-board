<?php
// config\presence-board.php

return [
    /*
    |--------------------------------------------------------------------------
    | Presence Board Configuration
    |--------------------------------------------------------------------------
    */

    // Number of employees per page
    'per_page' => env('PRESENCE_BOARD_PER_PAGE', 10),

    // Poll interval in seconds
    'poll_interval' => env('PRESENCE_BOARD_POLL_INTERVAL', 8),

    // Cache TTL in seconds (should match poll interval)
    'cache_ttl' => env('PRESENCE_BOARD_CACHE_TTL', 8),

    // Auto-rotate pages
    'auto_rotate' => env('PRESENCE_BOARD_AUTO_ROTATE', true),

    // Display settings
    'show_employee_id' => env('PRESENCE_BOARD_SHOW_EMPLOYEE_ID', true),
    'show_profile_photos' => env('PRESENCE_BOARD_SHOW_PHOTOS', true),
    
    // Inactivity reload time (minutes)
    'inactivity_reload' => env('PRESENCE_BOARD_INACTIVITY_RELOAD', 60),
];
