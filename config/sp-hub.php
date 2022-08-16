<?php

return [
    'rabbitmq' => [
        'host' => env('RABBITMQ_HOST', 'localhost'),
        'port' => env('RABBITMQ_PORT', '5672'),
        'user' => env('RABBITMQ_USER', 'admin'),
        'password' => env('RABBITMQ_PASSWORD', 'admin'),
        'virtualhost' => env('RABBITMQ_VIRTUALHOST', '/'),
        'exchange' => [
            'hub' => env('RABBITMQ_EXCHANGE_HUB', 'hub'),
        ],
        'queue' => [
            'hub' => env('RABBITMQ_QUEUE_HUB', 'hub.vendas'),
        ]
    ],
];
