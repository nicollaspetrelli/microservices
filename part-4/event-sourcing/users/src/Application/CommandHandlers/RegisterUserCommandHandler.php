<?php

declare(strict_types=1);

namespace Users\Application\CommandHandlers;

use Framework\Application\CommandHandlers\AbstractCommandHandler;
use Framework\Application\Service\EventBrokerInterface;
use Framework\Domain\Event\GenericErrorEvent;
use Users\Application\Commands\RegisterUserCommand;
use Users\Domain\User\EmailAlreadyExistsException;
use Users\Domain\User\Events\UserRegisteredEvent;
use Users\Domain\User\User;
use Users\Domain\User\UserStoreInterface;

class RegisterUserCommandHandler extends AbstractCommandHandler
{
    /**
     * @param  EventBrokerInterface  $broker
     * @param  UserStoreInterface    $userStore
     */
    public function __construct(
        EventBrokerInterface $broker,
        private UserStoreInterface $userStore
    ) {
        parent::__construct($broker);
    }

    /**
     * Handles the command
     *
     * @param  RegisterUserCommand  $command
     *
     * @return void
     * @throws \Exception
     */
    public function __invoke(RegisterUserCommand $command): void
    {
        $email = $command->getUserEmail();

        $user = new User(
            null,
            $command->getUserName(),
            $email
        );

        // @TODO check if email already exists
        if ($this->userStore->hasStreamForUser($user)) {
            $this->dispatchEvent(
                GenericErrorEvent::fromThrowable(new EmailAlreadyExistsException($email))
            );
            return;
        }

        $event = new UserRegisteredEvent($user);
        $this->userStore->write($event);
        $this->dispatchEvent($event);
    }
}
