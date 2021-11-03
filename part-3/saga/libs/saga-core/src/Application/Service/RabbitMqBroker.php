<?php

declare(strict_types=1);

namespace Framework\Core\Application\Service;

use Framework\Core\Domain\Event\EventInterface;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exchange\AMQPExchangeType;
use PhpAmqpLib\Message\AMQPMessage;
use stdClass;

class RabbitMqBroker implements EventBrokerInterface
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
     * RabbitMqService constructor.
     *
     * @param string $successQueue
     * @param string|null $failQueue
     * @param array $settings
     */
    public function __construct(
        private string $successQueue,
        private ?string $failQueue,
        array $settings
    ) {
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

        $this->channel->queue_declare(
            queue: $successQueue,
            passive: false,
            durable: true,
            exclusive: false,
            auto_delete: false
        );
        $this->channel->queue_bind(
            queue: $successQueue,
            exchange: $this->exchange,
            routing_key: $successQueue
        );

        if ($failQueue !== null) {
            $this->channel->queue_declare(
                queue: $failQueue,
                passive: false,
                durable: true,
                exclusive: false,
                auto_delete: false
            );
            $this->channel->queue_bind(
                queue: $failQueue,
                exchange: $this->exchange,
                routing_key: $failQueue
            );
        }
    }

    /**
     * Sends a success event to the broker
     *
     * @param  EventInterface  $event
     *
     * @return EventBrokerInterface
     */
    public function publishSuccessEvent(EventInterface $event): EventBrokerInterface
    {
        $this->publish(
            $this->successQueue,
            $event
        );

        return $this;
    }

    /**
     * Sends a fail event to the broker
     *
     * @param  EventInterface  $event
     *
     * @return EventBrokerInterface
     */
    public function publishFailEvent(EventInterface $event): EventBrokerInterface
    {
        if (empty($this->failQueue)) {
            // We could implement a Dead Letter Queue
            throw new \RuntimeException('No Fail Queue was provided');
        }
        $this->publish(
            $this->failQueue,
            $event
        );

        return $this;
    }

    /**
     * Publishes a message to the current exchange with the specified routing key
     *
     * @param string $routingKey
     * @param EventInterface $event
     *
     * @return $this
     */
    protected function publish(string $routingKey, EventInterface $event): self
    {
        $message = new AMQPMessage(
            \json_encode([
                'class' => \get_class($event),
                'event' => $event,
            ]),
            [
                'content_type' => 'application/json',
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
     * Consumes an event from the broker
     *
     * @param callable $callback
     * @param array $queues
     *
     * @return void
     * @throws \ErrorException
     */
    public function consumeEvent(callable $callback, array $queues): void
    {
        $channel = $this->channel;
        $callback = function (AMQPMessage $message) use ($callback) {
            $body = \json_decode($message->body);
            if (empty($body)) {
                // @TODO logging
                return;
            }
            echo "Received event:\n";
            echo \json_encode($body, JSON_PRETTY_PRINT);
            echo "\n";
            $callback($this->unserializeEvent($body));
        };

        foreach ($queues as $queue) {
            $channel->basic_consume(
                queue: $queue,
                no_ack: true,
                callback: $callback
            );
        }

        while ($channel->is_consuming()) {
            $channel->wait();
        }
    }

    /**
     * @param stdClass $data
     *
     * @return EventInterface
     */
    private function unserializeEvent(stdClass $data): EventInterface
    {
        if (!isset($data->class)) {
            throw new \UnexpectedValueException('Bad event received');
        }

        if ((!\class_exists($data->class)) || (!\is_subclass_of($data->class, EventInterface::class))) {
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
