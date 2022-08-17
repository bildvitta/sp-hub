<?php

namespace BildVitta\SpHub\Console\Commands\Messages;

use BildVitta\SpHub\Console\Commands\Messages\Resources\MessageProcessor;
use Illuminate\Console\Command;
use PhpAmqpLib\Connection\AMQPSSLConnection;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exception\AMQPExceptionInterface;
use PhpAmqpLib\Channel\AMQPChannel;
use Exception;

class HubMessageWorkerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rabbitmqworker:hub';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gets and processes messages';

    /**
     * @var AMQPStreamConnection
     */
    private $connection;

    /**
     * @var AMQPChannel
     */
    private $channel;

    /**
     * @var MessageProcessor
     */
    private MessageProcessor $messageProcessor;

    /**
     * @param MessageProcessor $messageProcessor
     */
    public function __construct(MessageProcessor $messageProcessor)
    {
        parent::__construct();
        $this->messageProcessor = $messageProcessor;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        while (true) {
            try {
                $this->process();
            } catch (AMQPExceptionInterface $exception) {
                $this->closeChannel();
                $this->closeConnection();
                sleep(5);
            }
        }
        return 0;
    }

    /**
     * @return void
     */
    private function process(): void
    {
        $this->connect();
        $this->channel = $this->connection->channel();
        
        $queueName = config('sp-hub.rabbitmq.queue.hub');
        $callback = [$this->messageProcessor, 'process'];
        $this->channel->basic_consume(
            queue: $queueName,
            callback: $callback
        );

        $this->channel->consume();
        
        $this->closeChannel();
        $this->closeConnection();
    }

    /**
     * @return void
     */
    private function closeChannel(): void
    {
        try {
            if ($this->channel) {
                $this->channel->close();
            }
        } catch (Exception $exception) {
        }
    }

    /**
     * @return void
     */
    private function closeConnection(): void
    {
        try {
            if ($this->connection) {
                $this->connection->close();
            }
        } catch (Exception $exception) {
        }
    }

    /**
     * @return void
     */
    private function connect(): void
    {
        $host = config('sp-hub.rabbitmq.host');
        $port = config('sp-hub.rabbitmq.port');
        $user = config('sp-hub.rabbitmq.user');
        $password = config('sp-hub.rabbitmq.password');
        $virtualhost = config('sp-hub.rabbitmq.virtualhost');
        $heartbeat = 20;
        $sslOptions = [
            'verify_peer' => false
        ];
        $options = [
            'heartbeat' => $heartbeat
        ];
        
        if (app()->isLocal()) {
            $this->connection = new AMQPStreamConnection(
                host: $host,
                port: $port,
                user: $user,
                password: $password,
                vhost: $virtualhost,
                heartbeat: $heartbeat
            );
        } else {
            $this->connection = new AMQPSSLConnection(
                host: $host,
                port: $port,
                user: $user,
                password: $password,
                vhost: $virtualhost,
                ssl_options: $sslOptions,
                options: $options
            );
        }
    }
}
