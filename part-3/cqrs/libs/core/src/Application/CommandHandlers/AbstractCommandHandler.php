<?php

declare(strict_types=1);

namespace Framework\Application\CommandHandlers;

use Framework\Application\Service\EventBrokerInterface;
use Framework\Domain\Event\EventInterface;

abstract class AbstractCommandHandler
{
    /**
     * @param  EventBrokerInterface  $broker
     */
    public function __construct(private EventBrokerInterface $broker)
    {
    }

    /**
     * @param  EventInterface  $event
     *
     * @return $this
     */
    protected function dispatchEvent(EventInterface $event): self
    {
        $this->broker->publishEvent($event);
        return $this;
    }
}
