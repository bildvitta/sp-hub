[![Latest Version on Packagist](https://img.shields.io/packagist/v/bildvitta/sp-hub.svg?style=flat-square)](https://packagist.org/packages/bildvitta/sp-hub)
[![Total Downloads](https://img.shields.io/packagist/dt/bildvitta/sp-hub.svg?style=flat-square)](https://packagist.org/packages/bildvitta/sp-hub)

## Introduction

The SP (Space Probe) package is responsible for collecting remote data updates for the module, keeping the data structure similar as possible, through the message broker.

## Installation

You can install the package via composer:

```bash 
composer require bildvitta/sp-hub:dev-develop
```

For everything to work perfectly in addition to having the settings file published in your application, run the command below:

```bash
php artisan sp:install
```

To configure the queues in RabbitMQ just run this command that it automatically creates based on the settings you passed in `config/sp-hub.php`

```bash
php artisan sp-hub:configure
```

## Configuration

This is the contents of the published config file:

```php
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
```

With the configuration file sp-hub.php published in your configuration folder it is necessary to create environment variables in your .env file: