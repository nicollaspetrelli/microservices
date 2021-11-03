<?php

namespace Framework\Service;

use Amp\Promise;
use Framework\Domain\Event\EventInterface;
use Framework\Domain\Model\ModelAggregateInterface;
use Prooph\EventStore\Async\ClientConnectionEventArgs;
use Prooph\EventStore\Async\EventStoreConnection;
use Prooph\EventStore\EndPoint;
use Prooph\EventStore\EventData;
use Prooph\EventStore\ExpectedVersion;
use Prooph\EventStoreClient\Internal\EventStoreNodeConnection;
use Prooph\EventStoreClient\Projections\QueryManager;
use Prooph\EventStoreClient\Projections\ProjectionsManager;
use Throwable;

class EventStoreDb
{
    /**
     * @var EndPoint
     */
    private EndPoint $httpEndpoint;

    /**
     * @param EventStoreConnection $connection
     * @param string $httpEndpointHost
     * @param int $httpEndpointPort
     */
    public function __construct(
        private EventStoreConnection $connection,
        private string $httpEndpointHost = 'localhost',
        private int $httpEndpointPort = 2113
    ) {
    }

    /**
     * @param callable $callback
     */
    public function connect(callable $callback): void
    {
        $this->connection->onConnected(function (ClientConnectionEventArgs $args) use ($callback) {
            $callback($this);
        });
        $this->connection->connectAsync();
    }

    /**
     * @param  string                   $stream
     * @param  ModelAggregateInterface  $model
     * @param  int                      $start
     * @param  int                      $count
     * @param  callable|null            $callback
     *
     * @return Promise
     */
    public function readAndApply(
        string $stream,
        ModelAggregateInterface $model,
        int $start = 0,
        int $count = 30,
        callable $callback = null
    ): Promise {
        return $this->read(
            $stream,
            function (\Framework\Domain\Event\EventInterface $event) use ($model, $callback) {
                $model->apply($event);
                if ($callback !== null) {
                    $callback($event, $model);
                }
            },
            $start,
            $count
        );
    }

    /**
     * @param string $stream
     * @param callable $onResolve
     * @param int $start
     * @param int $count
     *
     * @return Promise
     */
    public function read(
        string $stream,
        callable $onResolve,
        int $start = 0,
        int $count = 30
    ): Promise {
        $promise = $this->connection->readStreamEventsForwardAsync(
            $stream,
            $start,
            $count
        );
        $promise->onResolve(function (?Throwable $error, \Prooph\EventStore\StreamEventsSlice $slice) use ($onResolve) {
            if ($error) {
                throw $error;
            }
            foreach ($slice->events() as $resolved) {
                $recordedEvent = $resolved->event();
                $class = $recordedEvent->eventType();
                if ((!\class_exists($class)) || (!\is_subclass_of($class, EventInterface::class))) {
                    continue;
                }
                $data = $recordedEvent->data();
                if ($recordedEvent->isJson()) {
                    $data = \json_decode($data);
                }

                $event = $class::jsonUnserialize($data, $recordedEvent->eventId()->toString());
                $onResolve($event);
            }
        });
        return $promise;
    }

    /**
     * @param string $stream
     *
     * @param EventInterface $event
     *
     * @return Promise
     */
    public function write(
        string $stream,
        EventInterface $event
    ): Promise {
        return $this->connection->appendToStreamAsync(
            $stream,
            ExpectedVersion::ANY,
            [
                new EventData(
                    null,
                    \get_class($event),
                    true,
                    \json_encode($event)
                )
            ]
        );
    }

    /**
     * @param string $streamName
     *
     * @return bool
     */
    public function hasStream(string $streamName): bool
    {
        try {
            /** @var \Prooph\EventStore\RawStreamMetadataResult $metadata */
            $metadata = Promise\wait($this->connection->getRawStreamMetadataAsync($streamName));
            return (!$metadata->isStreamDeleted()) && (!empty($metadata->streamMetadata()));
        } catch (\Throwable) {
            return false;
        }
    }

    /**
     * @return ProjectionsManager
     */
    public function getProjectionsManager(): ProjectionsManager
    {
        if (!isset($this->projectionsManager)) {
            $this->projectionsManager = new ProjectionsManager(
                $this->getHttpEndpoint(),
                5000, // $operationTimeout
                false, // $tlsTerminatedEndpoint
                true, // $verifyPeer
                ($this->connection instanceof EventStoreNodeConnection)
                    ? $this->connection->connectionSettings()->defaultUserCredentials()
                    : null
            );
        }
        return $this->projectionsManager;
    }

    /**
     * @return QueryManager
     */
    public function getQueryManager(): QueryManager
    {
        if (!isset($this->queryManager)) {
            $this->queryManager = new QueryManager(
                $this->getHttpEndpoint(),
                5000,
                5000,
                false, // $tlsTerminatedEndpoint
                true, // $verifyPeer
                ($this->connection instanceof EventStoreNodeConnection)
                    ? $this->connection->connectionSettings()->defaultUserCredentials()
                    : null
            );
        }
        return $this->queryManager;
    }

    /**
     * @return EndPoint
     */
    private function getHttpEndpoint(): Endpoint
    {
        if (!isset($this->httpEndpoint)) {
            $this->httpEndpoint = new EndPoint(
                $this->httpEndpointHost,
                $this->httpEndpointPort
            );
        }
        return $this->httpEndpoint;
    }
}
