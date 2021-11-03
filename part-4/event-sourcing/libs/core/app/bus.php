<?php

declare(strict_types=1);

use Framework\Application\EventHandlers\UnknownEventHandler;
use Framework\Domain\Event\GenericErrorEvent;
use Framework\Domain\Event\UnknownEvent;

return [
    GenericErrorEvent::class => [UnknownEventHandler::class],
    UnknownEvent::class      => [UnknownEventHandler::class],
];
