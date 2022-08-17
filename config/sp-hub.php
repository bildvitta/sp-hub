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
];
