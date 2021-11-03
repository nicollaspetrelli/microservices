<?php

declare(strict_types=1);

namespace Framework\Core\Application\Service;

use Framework\Core\Domain\Event\EventInterface;

interface EventBrokerInterface
{

    /**
     * Sends a success event to the broker
     *
     * @param  EventInterface  $event
     *
     * @return EventBrokerInterface
     */
    public function publishSuccessEvent(EventInterface $event): EventBrokerInterface;

    /**
     * Sends a fail event to the broker
     *
     * @param  EventInterface  $event
     *
     * @return EventBrokerInterface
     */
    public function publishFailEvent(EventInterface $event): EventBrokerInterface;

    /**
     * nullumes an event from the broker
     *
     * @param callable $callback
     * @param array $queues
     *
     * @return void
     * @throws \ErrorException
     */
    public function consumeEvent(callable $callback, array $queues): void;

}
