<?php

declare(strict_types=1);

namespace Framework\Core\Domain\Event;

use Charm\Id;
use stdClass;

class UnknownEvent implements EventInterface
{

    /**
     * @var string
     */
    private string $uuid;

    /**
     * @param  EventInterface  $event
     */
    public function __construct(private EventInterface $event)
    {
    }

    /**
     * @return string
     */
    public function getUuid(): string
    {
        if (!isset($this->uuid)) {
            $this->uuid = Id::uuid4();
        }
        return $this->uuid;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'class' => \get_class($this->event),
            'event' => $this->event,
        ];
    }

    /**
     * @param  stdClass  $data
     *
     * @return EventInterface
     */
    public static function jsonUnserialize(stdClass $data): EventInterface
    {
        if (!isset($data->class)) {
            throw new \InvalidArgumentException('Bad event received: no class provided');
        }
        if (!isset($data->event)) {
            throw new \InvalidArgumentException('Bad event received: no event payload provided');
        }
        if (!\is_subclass_of($data->class, EventInterface::class)) {
            throw new \InvalidArgumentException('Bad event received: invalid class provided');
        }
        return new UnknownEvent(
            $data->class::jsonUnserialize($data->event)
        );
    }

}
