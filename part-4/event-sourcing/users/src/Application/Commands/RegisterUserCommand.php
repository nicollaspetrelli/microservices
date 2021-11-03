<?php

declare(strict_types=1);

namespace Users\Application\Commands;

use Framework\Application\Commands\AbstractCommand;

final class RegisterUserCommand extends AbstractCommand
{

    /**
     * @param string $name
     * @param string $email
     */
    public function __construct(string $name, string $email)
    {
        if (!\filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException("Bad email provided: {$email}");
        }

        $name = \trim($name);
        if (empty($name)) {
            throw new \InvalidArgumentException('Please provide your name');
        }

        $this
            ->set('name', $name)
            ->set('email', $email);
    }

    /**
     * @return string
     */
    public function getUserName(): string
    {
        return $this->get('name');
    }

    /**
     * @return string
     */
    public function getUserEmail(): string
    {
        return $this->get('email');
    }

}
