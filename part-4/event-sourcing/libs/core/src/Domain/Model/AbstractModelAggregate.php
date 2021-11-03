<?php

declare(strict_types=1);

namespace Framework\Domain\Model;

use Framework\Domain\Event\EventInterface;
use Ramsey\Uuid\Uuid;

abstract class AbstractModelAggregate implements ModelAggregateInterface
{
    /**
     * @var string|null
     */
    protected ?string $id;

    /**
     * @var array
     */
    protected array $eventHandlers = [];

    /**
     * @param  string|null  $id
     *
     * @throws \Exception
     */
    public function __construct(?string $id)
    {
        $this->setId($id ?? Uuid::uuid4()->toString());
    }

    /**
     * Sets the user ID
     *
     * @param  string  $id
     *
     * @return ModelAggregateInterface
     */
    public function setId(string $id): ModelAggregateInterface
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @param  EventInterface  $event
     *
     * @return ModelAggregateInterface
     */
    public function apply(EventInterface $event): ModelAggregateInterface
    {
        $methodName = $this->getMethodNameForEvent($event);
        if (!\method_exists($this, $methodName)) {
            throw new \DomainException(
                \get_called_class() . ' cannot apply event ' . \get_class($event)
            );
        }

        $this->{$methodName}($event);

        return $this;
    }

    /**
     * @param  EventInterface  $event
     *
     * @return string
     */
    protected function getMethodNameForEvent(EventInterface $event): string
    {
        $class = \get_class($event);
        if (isset($this->eventHandlers[$class])) {
            return $this->eventHandlers[$class];
        }

        $class = \explode('\\', $class);
        return 'handle' . \end($class);
    }

}
