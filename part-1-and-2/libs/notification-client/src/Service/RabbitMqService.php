<?php

declare(strict_types=1);

namespace Vcampitelli\Framework\Notification\Service;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exchange\AMQPExchangeType;
use PhpAmqpLib\Message\AMQPMessage;
use Vcampitelli\Framework\Notification\NotificationInterface;

class RabbitMqService implements NotificationServiceInterface
{
    /**
     * @var AMQPStreamConnection
     */
    private AMQPStreamConnection $connection;

    /**
     * @var AMQPChannel
     */
    private AMQPChannel $channel;

    /**
     * @var string
     */
    private string $exchange;

    /**
     * Declared queues
     *
     * @var array
     */
    private array $queues = [];

    /**
     * RabbitMqService constructor.
     *
     * @param string $queue
     * @param array $settings
     */
    public function __construct(string $queue, array $settings)
    {
        $this->connection = new AMQPStreamConnection(
            $settings['host'],
            $settings['port'] ?? 5672,
            $settings['user'] ?? 'guest',
            $settings['pass'] ?? 'guest',
            $settings['vhost'] ?? '/'
        );
        $this->channel = $this->connection->channel();

        $this->exchange = $settings['exchange'] ?? 'router';
        $this->channel->exchange_declare($this->exchange, AMQPExchangeType::DIRECT, false, true, false);

        $this->channel->queue_declare($queue, false,true, false, false);
        $this->channel->queue_bind($queue, $this->exchange);
    }

    /**
     * Sends a message to the current exchange
     *
     * @param NotificationInterface $message
     *
     * @return NotificationServiceInterface
     */
    public function send(NotificationInterface $message): NotificationServiceInterface
    {
        $user = $message->getUser();
        $message = new AMQPMessage(
            \json_encode([
                'to' => $user->getId(),
                'subject' => $message->getSubject(),
                'body' => $message->getBody(),
            ]),
            [
                'content_type' => 'application/json',
                'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,
            ]
        );
        $this->channel->basic_publish($message, $this->exchange);

        return $this;
    }

    /**
     * @throws \Exception
     */
    public function __destruct()
    {
        $this->channel->close();
        $this->connection->close();
    }
}
