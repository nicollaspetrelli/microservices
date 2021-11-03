<?php

declare(strict_types=1);

namespace Framework\Core\Application\EventHandlers;

use Framework\Core\Domain\Event\EventInterface;
use Framework\Core\Domain\Event\GenericErrorEvent;
use Psr\Log\LoggerInterface;

class UnknownEventHandler
{
    /**
     * @param  LoggerInterface  $logger
     */
    public function __construct(private LoggerInterface $logger)
    {
    }

    /**
     * @param  EventInterface  $event
     */
    public function __invoke(EventInterface $event): void
    {
        if ($event instanceof GenericErrorEvent) {
            $this->logger->critical(
                'A generic ' . \get_class($event) . ' error occurred: ' . \json_encode($event)
            );
            return;
        }

        $this->logger->error(
            'Received an unknown ' . \get_class($event) . ' event: ' . \json_encode($event)
        );
    }
}
