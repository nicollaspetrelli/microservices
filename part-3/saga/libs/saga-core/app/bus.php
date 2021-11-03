<?php

declare(strict_types=1);

use Framework\Core\Application\EventHandlers\UnknownEventHandler;
use Framework\Core\Domain\Event\GenericErrorEvent;
use Framework\Core\Domain\Event\UnknownEvent;

return [
    GenericErrorEvent::class => [UnknownEventHandler::class],
    UnknownEvent::class      => [UnknownEventHandler::class],
];
