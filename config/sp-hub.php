<?php

return [
    'rabbitmq' => [
        'host' => env('RABBITMQ_HOST'),
        'port' => env('RABBITMQ_PORT'),
        'user' => env('RABBITMQ_USER'),
        'password' => env('RABBITMQ_PASSWORD'),
        'virtualhost' => env('RABBITMQ_VIRTUALHOST', '/'),
        'exchange' => [
            'hub' => env('RABBITMQ_EXCHANGE_HUB', 'hub'),
        ],
        'queue' => [
            'hub' => env('RABBITMQ_QUEUE_HUB'),
        ]
    ],
    'db' => [
        'host' => env('HUB_DB_HOST', '127.0.0.1'),
        'port' => env('HUB_DB_PORT', '3306'),
        'database' => env('HUB_DB_DATABASE', 'forge'),
        'username' => env('HUB_DB_USERNAME', 'forge'),
        'password' => env('HUB_DB_PASSWORD', ''),
    ],
];
