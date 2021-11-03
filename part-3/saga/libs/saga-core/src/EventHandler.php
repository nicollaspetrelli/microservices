<?php

declare(strict_types=1);

namespace Framework\Core;

use DI\ContainerBuilder;
use Framework\Core\Application\Service\EventBrokerInterface;
use Framework\Core\Domain\Event\EventInterface;
use Framework\Core\Domain\Event\GenericErrorEvent;
use Framework\Core\Domain\Event\UnknownEvent;
use Symfony\Component\Messenger\Exception\NoHandlerForMessageException;
use Symfony\Component\Messenger\MessageBus;

/**
 * Consumer for Events
 */
class EventHandler
{

    /**
     * @param string                           $dir
     * @param \Framework\Core\ContainerBuilder $containerBuilder
     */
    public static function loadEventBroker(string $dir, ContainerBuilder $containerBuilder): void
    {
        // Set up framework bus handlers
        $busDefinitions = require __DIR__ . '/../app/bus.php';
        // Set up client bus handlers
        $file = $dir . '/../app/bus.php';
        if (\is_file($file)) {
            $definitions = require $file;
            if (\is_array($definitions)) {
                $busDefinitions += $definitions;
            }
        }

        // Set up framework dependencies
        $dependencies = require __DIR__ . '/../app/dependencies.php';
        $dependencies($containerBuilder, $busDefinitions);
        // Set up client dependencies
        $file = $dir . '/../app/dependencies.php';
        if (\is_file($file)) {
            $dependencies = require $file;
            $dependencies($containerBuilder);
        }
    }

    /**
     * Consumes Events from the queue
     *
     * @param string $dir
     * @param array $queues
     *
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     * @throws \ErrorException
     */
    public static function run(string $dir, array $queues): void
    {
        // Instantiate PHP-DI ContainerBuilder
        $containerBuilder = new ContainerBuilder();

        self::loadEventBroker($dir, $containerBuilder);

        // Build PHP-DI Container instance
        $container = $containerBuilder->build();

        /** @var MessageBus $messageBus */
        $messageBus = $container->get(MessageBus::class);

        /** @var EventBrokerInterface $eventBroker */
        $eventBroker = $container->get(EventBrokerInterface::class);

        $eventBroker->consumeEvent(function (EventInterface $event) use ($messageBus, $eventBroker) {
            try {
                $messageBus->dispatch($event);
            } catch (NoHandlerForMessageException) {
                $eventBroker->publishEvent(new UnknownEvent($event));
            } catch (\Throwable $throwable) {
                var_dump($throwable->getMessage(), $throwable->getTraceAsString()); die(1);
                $eventBroker->publishEvent(
                    GenericErrorEvent::fromThrowable($throwable)
                );
            }
        }, $queues);
    }

}
