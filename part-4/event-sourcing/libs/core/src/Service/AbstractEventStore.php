<?php

namespace Framework\Service;

use Amp\Promise;
use Prooph\EventStore\Projections\State;
use Prooph\EventStoreClient\Exception\ProjectionCommandFailed;
use Throwable;

abstract class AbstractEventStore
{
    /**
     * @param EventStoreDb $db
     */
    public function __construct(
        private EventStoreDb $db
    ) {
    }

    /**
     * Retrieve data from a query
     *
     * @param string $name
     * @return Promise
     */
    protected function getQueryState(string $name): Promise
    {
        return $this->getEventStore()
            ->getProjectionsManager()
            ->getStateAsync($name);
    }

    /**
     * Deletes a query
     *
     * @param  string  $name
     *
     * @return Promise
     */
    protected function deleteQuery(string $name): Promise
    {
        return $this->getEventStore()
            ->getProjectionsManager()
            ->deleteAsync($name);
    }

    /**
     * Retrieve data from a query
     *
     * @param string $name
     * @param string $query
     * @param int $initialPollingDelay
     * @param int $maximumPollingDelay
     * @return Promise
     */
    protected function executeQuery(
        string $name,
        string $query,
        int $initialPollingDelay = 0,
        int $maximumPollingDelay = 0
    ): Promise {
        return $this->getEventStore()->getQueryManager()->executeAsync(
            $name,
            $query,
            $initialPollingDelay,
            $maximumPollingDelay
        );
    }

    /**
     * @param string $name
     * @param string $query
     * @param callable $callback
     * @return $this
     */
    protected function retrieveQueryStateOrCreate(
        string $name,
        string $query,
        callable $callback
    ): self {
        $this->getQueryState($name)
            ->onResolve(function (?Throwable $error, ?State $state) use ($name, $query, $callback) {
                if ($error !== null) {
                    if (($error instanceof ProjectionCommandFailed) && ($error->httpStatusCode() === 404)) {
                        // Query doesn't exist, let's create it instead
                        $this->executeQuery(
                            $name,
                            $query
                        )->onResolve(function (?Throwable $error, ?State $state) use ($callback) {
                            if ($error !== null) {
                                throw $error;
                            }

                            $callback($state);
                        });
                        return;
                    }

                    throw $error;
                }

                // Query already exists
                $callback($state);
            });
        return $this;
    }

    /**
     * @return EventStoreDb
     */
    protected function getEventStore(): EventStoreDb
    {
        return $this->db;
    }
}
