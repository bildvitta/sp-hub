<?php

namespace BildVitta\SpHub\Console\Commands;

use Illuminate\Console\Command;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class ConfigureRabbitMQ extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sp-hub:configure';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates initial config for message broker';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if (!config('rabbitmq.active', false)) {
            $this->info('RabbitMQ não está ativado! Ignorando configuração inicial...');
            return 0;
        }

        $host = config('sp-hub.rabbitmq.host');
        $port = config('sp-hub.rabbitmq.port');
        $user = config('sp-hub.rabbitmq.user');
        $password = config('sp-hub.rabbitmq.password');
        $vhost = config('sp-hub.rabbitmq.virtualhost');

        $exchangeName = config('sp-hub.rabbitmq.exchange.hub');
        $queueName = config('sp-hub.rabbitmq.queue.hub');

        $connection = new AMQPStreamConnection($host, $port, $user, $password, $vhost);
        $channel = $connection->channel();

        $channel->exchange_declare($exchangeName, 'fanout', false, true, false);
        $channel->queue_declare($queueName, false, true, false, false);
        $channel->queue_bind($queueName, $exchangeName);

        $channel->close();
        $connection->close();

        $this->info('Configuração do RabbitMQ efetuada com sucesso!');

        return 0;
    }
}
