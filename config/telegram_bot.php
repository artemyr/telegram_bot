<?php

return [
    'debug' => false,
    'auth' => [
        'register_pass' => env('TELEGRAM_REGISTER_PASS', 'password'),
        'user_state_lock_period' => 3600 * 24 * 30
    ],
    'messages' => [
        // jobs/runtime
        'driver' => env('TELEGRAM_MESSAGES_DRIVER', 'jobs'),
    ],
    'serverip' => env('SERVER_IP'),
    'bots' => [
        'schedule' => [
            'token' => env('TELEGRAM_SCHEDULE_BOT_TOKEN', ''),
            'factory' => \Domain\Schedule\Factory\ScheduleBotFactory::class,
            'menu' => \Domain\Schedule\Menu\MenuFactory::class,
        ],
        'travel' => [
            'token' => env('TELEGRAM_TRAVEL_BOT_TOKEN', ''),
            'factory' => \Domain\Travel\Factory\TravelBotFactory::class,
            'menu' => \Domain\Travel\Menu\MenuFactory::class,
        ],
    ]
];
