<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Real-time Configuration
    |--------------------------------------------------------------------------
    |
    | FluxChat supports both standard Livewire (polling) and real-time 
    | messaging via Laravel Reverb. Configure these settings to enable
    | or disable real-time features based on your needs.
    |
    */

    'realtime' => [
        'enabled' => env('FLUXCHAT_REALTIME_ENABLED', false),
        'driver' => env('FLUXCHAT_REALTIME_DRIVER', 'reverb'),
        'typing_indicators' => env('FLUXCHAT_TYPING_INDICATORS', true),
        'online_status' => env('FLUXCHAT_ONLINE_STATUS', true),
        'auto_refresh_interval' => env('FLUXCHAT_AUTO_REFRESH', 5), // seconds
    ],

    /*
    |--------------------------------------------------------------------------
    | UI Configuration
    |--------------------------------------------------------------------------
    |
    | Customize the appearance and behavior of FluxChat components.
    |
    */

    'ui' => [
        'theme' => env('FLUXCHAT_THEME', 'dark'), // dark, light
        'avatar_size' => env('FLUXCHAT_AVATAR_SIZE', 'sm'),
        'compact_mode' => env('FLUXCHAT_COMPACT_MODE', false),
        'show_timestamps' => env('FLUXCHAT_SHOW_TIMESTAMPS', true),
        'auto_scroll' => env('FLUXCHAT_AUTO_SCROLL', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Message Configuration
    |--------------------------------------------------------------------------
    |
    | Configure message handling and storage settings.
    |
    */

    'messages' => [
        'max_length' => env('FLUXCHAT_MAX_MESSAGE_LENGTH', 1000),
        'file_uploads' => env('FLUXCHAT_FILE_UPLOADS', false),
        'emoji_support' => env('FLUXCHAT_EMOJI_SUPPORT', true),
        'markdown_support' => env('FLUXCHAT_MARKDOWN_SUPPORT', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Database Configuration
    |--------------------------------------------------------------------------
    |
    | Configure database table names and relationships.
    |
    */

    'database' => [
        'tables' => [
            'conversations' => 'fluxchat_conversations',
            'messages' => 'fluxchat_messages',
            'participants' => 'fluxchat_participants',
        ],
        'morphs' => [
            'sendable' => 'sendable', // User model morph name
            'receivable' => 'receivable', // Contact/User model morph name
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Broadcasting Configuration
    |--------------------------------------------------------------------------
    |
    | Configure how FluxChat broadcasts real-time events.
    |
    */

    'broadcasting' => [
        'connection' => env('FLUXCHAT_BROADCAST_CONNECTION', 'redis'),
        'queue' => env('FLUXCHAT_BROADCAST_QUEUE', 'default'),
        'channel_prefix' => env('FLUXCHAT_CHANNEL_PREFIX', 'fluxchat'),
    ],
];
