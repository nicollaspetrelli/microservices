<?php

declare(strict_types=1);

namespace Framework\Application\Service;

use Framework\Domain\Event\EventInterface;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exchange\AMQPExchangeType;
use PhpAmqpLib\Message\AMQPMessage;
use Framework\Application\Commands\CommandInterface;
use stdClass;

class RabbitMqBroker implements CommandBrokerInterface, EventBrokerInterface
{

    /**
     * @var string
     */
    const ROUTING_KEY_COMMAND = 'command';

    /**
     * @var string
     */
    const ROUTING_KEY_EVENT = 'event';

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
     * @var string
     */
    private string $commandQueue;

    /**
     * @var string
     */
    private string $eventQueue;

    /**
     * RabbitMqService constructor.
     *
     * @param  string  $commandQueue
     * @param  string  $eventQueue
     * @param  array   $settings
     */
    public function __construct(string $commandQueue, string $eventQueue, array $settings)
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
        $this->channel->exchange_declare(
            exchange: $this->exchange,
            type: AMQPExchangeType::DIRECT,
            passive: false,
            durable: true,
            auto_delete: false
        );

        $this->commandQueue = $commandQueue;
        $this->channel->queue_declare(
            queue: $commandQueue,
            passive: false,
            durable: true,
            exclusive: false,
            auto_delete: false
        );
        $this->channel->queue_bind(
            queue: $commandQueue,
            exchange: $this->exchange,
            routing_key: self::ROUTING_KEY_COMMAND
        );

        $this->eventQueue = $eventQueue;
        $this->channel->queue_declare(
            queue: $eventQueue,
            passive: false,
            durable: true,
            exclusive: false,
            auto_delete: false
        );
        $this->channel->queue_bind(
            queue: $eventQueue,
            exchange: $this->exchange,
            routing_key: self::ROUTING_KEY_EVENT
        );
    }

    /**
     * Sends a command to the broker
     *
     * @param  CommandInterface  $command
     *
     * @return CommandBrokerInterface
     */
    public function publishCommand(CommandInterface $command): CommandBrokerInterface
    {
        $this->publish(
            self::ROUTING_KEY_COMMAND,
            $this->serializeCommand($command)
        );
        return $this;
    }

    /**
     * Sends an event to the broker
     *
     * @param  EventInterface  $event
     *
     * @return EventBrokerInterface
     */
    public function publishEvent(EventInterface $event): EventBrokerInterface
    {
        $this->publish(
            self::ROUTING_KEY_EVENT,
            [
                'class' => \get_class($event),
                'event' => $event,
            ]
        );
        return $this;
    }

    /**
     * Publishes a message to the current exchange with the specified routing key
     *
     * @param  string  $routingKey
     * @param          $message
     *
     * @return $this
     */
    protected function publish(string $routingKey, $message): self
    {
        $message = new AMQPMessage(
            \json_encode($message),
            [
                'content_type'  => 'application/json',
                'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,
            ]
        );
        $this->channel->basic_publish(
            msg: $message,
            exchange: $this->exchange,
            routing_key: $routingKey
        );

        return $this;
    }

    /**
     * Consumes a command from the broker
     *
     * @param  callable  $callback
     *
     * @return void
     * @throws \ErrorException
     */
    public function consumeCommand(callable $callback): void
    {
        $channel = $this->channel;
        $channel->basic_consume(
            queue: $this->commandQueue,
            no_ack: true,
            callback: function (AMQPMessage $message) use ($callback) {
                $body = \json_decode($message->body);
                if (empty($body)) {
                    // @TODO logging
                    return;
                }
                echo "Received command:\n";
                echo \json_encode($body, JSON_PRETTY_PRINT);
                echo "\n";
                $callback($this->unserializeCommand($body));
            }
        );

        while ($channel->is_consuming()) {
            $channel->wait();
        }
    }

    /**
     * Consumes an event from the broker
     *
     * @param  callable  $callback
     *
     * @return void
     * @throws \ErrorException
     */
    public function consumeEvent(callable $callback): void
    {
        $channel = $this->channel;
        $channel->basic_consume(
            queue: $this->eventQueue,
            no_ack: true,
            callback: function (AMQPMessage $message) use ($callback) {
                $body = \json_decode($message->body);
                if (empty($body)) {
                    // @TODO logging
                    return;
                }
                echo "Received event:\n";
                echo \json_encode($body, JSON_PRETTY_PRINT);
                echo "\n";
                $callback($this->unserializeEvent($body));
            }
        );

        while ($channel->is_consuming()) {
            $channel->wait();
        }
    }

    /**
     * @param  CommandInterface  $command
     *
     * @return array
     */
    private function serializeCommand(CommandInterface $command): array
    {
        return [
            'class'   => \get_class($command),
            'command' => $command,
        ];
    }

    /**
     * @param  stdClass  $data
     *
     * @return CommandInterface
     */
    private function unserializeCommand(stdClass $data): CommandInterface
    {
        if (!isset($data->class)) {
            throw new \UnexpectedValueException('Bad command received');
        }

        if (!\is_subclass_of($data->class, CommandInterface::class)) {
            throw new \UnexpectedValueException('Bad command received: ' . $data->class);
        }

        return $data->class::jsonUnserialize($data->command);
    }

    /**
     * @param  stdClass  $data
     *
     * @return EventInterface
     */
    private function unserializeEvent(stdClass $data): EventInterface
    {
        if (!isset($data->class)) {
            throw new \UnexpectedValueException('Bad event received');
        }

        if (!\is_subclass_of($data->class, EventInterface::class)) {
            throw new \UnexpectedValueException('Bad event received: ' . $data->class);
        }

        return $data->class::jsonUnserialize($data->event);
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
