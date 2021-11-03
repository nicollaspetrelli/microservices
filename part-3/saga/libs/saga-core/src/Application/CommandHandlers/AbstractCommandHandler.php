<?php

declare(strict_types=1);

namespace Framework\Core\Application\CommandHandlers;

use Framework\Core\Application\Service\EventBrokerInterface;
use Framework\Core\Domain\Event\EventInterface;

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
