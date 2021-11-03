<?php

declare(strict_types=1);

namespace Framework\Application\Service;

use Framework\Application\Commands\CommandInterface;

interface CommandBrokerInterface
{

    /**
     * Sends a command to the broker
     *
     * @param CommandInterface $command
     *
     * @return CommandBrokerInterface
     */
    public function publishCommand(CommandInterface $command): CommandBrokerInterface;

    /**
     * Consumes a command from the broker
     *
     * @param callable $callback
     *
     * @return void
     * @throws \ErrorException
     */
    public function consumeCommand(callable $callback): void;

}
