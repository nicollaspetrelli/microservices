<?php

declare(strict_types=1);

namespace Framework\Application\Service;

use Framework\Domain\Event\EventInterface;

interface EventBrokerInterface
{

    /**
     * Sends an event to the broker
     *
     * @param  EventInterface  $event
     *
     * @return EventBrokerInterface
     */
    public function publishEvent(EventInterface $event): EventBrokerInterface;

    /**
     * Consumes an event from the broker
     *
     * @param callable $callback
     *
     * @return void
     * @throws \ErrorException
     */
    public function consumeEvent(callable $callback): void;

}
