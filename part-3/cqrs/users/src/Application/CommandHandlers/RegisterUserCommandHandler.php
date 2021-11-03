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
use Users\Domain\User\UserRepositoryInterface;

class RegisterUserCommandHandler extends AbstractCommandHandler
{
    /**
     * @param  EventBrokerInterface     $broker
     * @param  UserRepositoryInterface  $userRepository
     */
    public function __construct(
        EventBrokerInterface $broker,
        private UserRepositoryInterface $userRepository
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

        try {
            $this->userRepository->persist($user);
        } catch (\Exception $e) {
            if (\str_contains($e->getMessage(), ' SQLSTATE[23505]: ')) {
                $this->dispatchEvent(
                    GenericErrorEvent::fromThrowable(new EmailAlreadyExistsException($email))
                );
                return;
            }

            throw $e;
        }

        $this->dispatchEvent(new UserRegisteredEvent($user));
    }
}
