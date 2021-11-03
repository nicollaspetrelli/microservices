<?php

declare(strict_types=1);

use Users\Application\CommandHandlers\RegisterUserCommandHandler;
use Users\Application\Commands\RegisterUserCommand;
use Users\Application\EventHandlers\UserRegisteredEventHandler;
use Users\Domain\User\Events\UserRegisteredEvent;

return [
    RegisterUserCommand::class => [RegisterUserCommandHandler::class],
    UserRegisteredEvent::class => [UserRegisteredEventHandler::class],
];
