<?php

declare(strict_types=1);

namespace Framework;

use Framework\Application\Service\EventBrokerInterface;
use Framework\Domain\Event\EventInterface;
use Framework\Domain\Event\GenericErrorEvent;
use Framework\Domain\Event\UnknownEvent;
use Symfony\Component\Messenger\Exception\NoHandlerForMessageException;
use Symfony\Component\Messenger\MessageBus;

/**
 * Consumer for Events
 */
class EventHandler extends AbstractBootstrapper
{

    /**
     * Consumes Events from the queue
     *
     * @param  string  $dir
     * @param  bool    $cache
     *
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     * @throws \ErrorException
     */
    public static function run(string $dir, bool $cache = false): void
    {
        $container = self::bootstrap($dir, $cache);

        /** @var MessageBus $messageBus */
        $messageBus = $container->get(MessageBus::class);

        /** @var EventBrokerInterface $eventBroker */
        $eventBroker = $container->get(EventBrokerInterface::class);

        $eventBroker->consumeEvent(function (EventInterface $event) use ($messageBus, $eventBroker) {
            try {
                $messageBus->dispatch($event);
            } catch (NoHandlerForMessageException) {
                $eventBroker->publishEvent(
                    new UnknownEvent($event)
                );
            } catch (\Throwable $throwable) {
                $eventBroker->publishEvent(
                    GenericErrorEvent::fromThrowable($throwable)
                );
            }
        });
    }

}
