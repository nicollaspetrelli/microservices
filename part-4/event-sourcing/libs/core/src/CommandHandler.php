<?php

declare(strict_types=1);

namespace Framework;

use Framework\Application\Service\EventBrokerInterface;
use Framework\Domain\Event\GenericErrorEvent;
use Symfony\Component\Messenger\MessageBus;
use Framework\Application\Commands\CommandInterface;
use Framework\Application\Service\CommandBrokerInterface;

/**
 * Consumer for Broker commands
 */
class CommandHandler extends AbstractBootstrapper
{

    /**
     * Consumes Command from the Queue
     *
     * @param  string  $dir
     * @param  bool    $cache
     *
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     */
    public static function run(string $dir, bool $cache = false): void
    {
        $container = self::bootstrap($dir, $cache);

        /** @var MessageBus $messageBus */
        $messageBus = $container->get(MessageBus::class);

        /** @var CommandBrokerInterface $eventBroker */
        $commandBroker = $container->get(CommandBrokerInterface::class);

        /** @var EventBrokerInterface $eventBroker */
        $eventBroker = $container->get(EventBrokerInterface::class);

        $commandBroker->consumeCommand(function (CommandInterface $command) use ($messageBus, $eventBroker) {
            try {
                $messageBus->dispatch($command);
            } catch (\Throwable $throwable) {
                $eventBroker->publishEvent(
                    GenericErrorEvent::fromThrowable($throwable)
                );
            }
        });
    }

}
