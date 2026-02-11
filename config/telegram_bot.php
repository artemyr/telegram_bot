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
    'menu' => [
        'schedule' => \Domain\Schedule\Menu\MenuFactory::class,
        'travel' => \Domain\Travel\Menu\MenuFactory::class,
    ],
    'bots' => [
        'schedule' => [
            'username' => 'artat_project_bot',
            'token' => env('TELEGRAM_SCHEDULE_BOT_TOKEN', ''),
            'factory' => \Domain\Schedule\Factory\ScheduleBotFactory::class
        ],
        'travel' => [
            'username' => 'travel_with_new_friends_bot',
            'token' => env('TELEGRAM_TRAVEL_BOT_TOKEN', ''),
            'factory' => \Domain\Travel\Factory\TravelBotFactory::class
        ],
    ]
];
