<?php

declare(strict_types=1);

namespace Framework\Application\Actions;

use Framework\Application\Commands\CommandInterface;
use Framework\Application\Service\CommandBrokerInterface;
use Psr\Log\LoggerInterface;

abstract class CommandAction extends Action
{
    /**
     * @param  \Psr\Log\LoggerInterface                               $logger
     * @param  \Framework\Application\Service\CommandBrokerInterface  $broker
     */
    public function __construct(
        LoggerInterface $logger,
        private CommandBrokerInterface $broker
    ) {
        parent::__construct($logger);
    }

    /**
     * @param  CommandInterface  $command
     *
     * @return $this
     */
    protected function dispatchCommand(CommandInterface $command): self
    {
        $this->broker->publishCommand($command);
        return $this;
    }

}
