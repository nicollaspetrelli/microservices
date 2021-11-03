<?php

declare(strict_types=1);

namespace Users\Domain\User;

use Framework\Domain\Model\AbstractModelAggregate;
use Users\Domain\User\Events\EmailChangedEvent;
use Users\Domain\User\Events\NameChangedEvent;
use Users\Domain\User\Events\UserRegisteredEvent;

class User extends AbstractModelAggregate
{

    /**
     * @param string|null $id
     * @param string   $name
     * @param string   $email
     */
    public function __construct(
      ?string $id,
      private string $name,
      private string $email
    ) {
        parent::__construct($id);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
          'id'    => $this->getId(),
          'name'  => $this->name,
          'email' => $this->email,
        ];
    }

    /**
     * @param  UserRegisteredEvent  $event
     */
    protected function handleUserRegisteredEvent(UserRegisteredEvent $event): void
    {
        $user = $event->getUser();
        $this->id = $user->getId();
        $this->name = $user->getName();
        $this->email = $user->getEmail();
    }

    /**
     * @param  EmailChangedEvent  $event
     */
    protected function handleEmailChangedEvent(EmailChangedEvent $event): void
    {
        $this->email = $event->getEmail();
    }

    /**
     * @param  NameChangedEvent  $event
     */
    protected function handleNameChangedEvent(NameChangedEvent $event): void
    {
        $this->name = $event->getName();
    }

}
