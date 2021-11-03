<?php

declare(strict_types=1);

namespace Framework\Core\Domain\Event;

use Charm\Id;
use stdClass;
use Throwable;

class GenericErrorEvent implements EventInterface
{

    /**
     * @var string
     */
    private string $uuid;

    /**
     * @param  string       $message
     * @param  string|null  $class
     */
    public function __construct(
        private string $message,
        private ?string $class = null
    ) {
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
     * Creates an Event object from a Throwable
     *
     * @param  Throwable  $throwable
     *
     * @return GenericErrorEvent
     */
    public static function fromThrowable(Throwable $throwable): GenericErrorEvent
    {
        return new GenericErrorEvent($throwable->getMessage(), \get_class($throwable));
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        $data = [
            'message' => $this->message,
        ];
        if (!empty($this->class)) {
            $data['class'] = $this->class;
        }
        return $data;
    }

    /**
     * @param  stdClass  $data
     *
     * @return EventInterface
     */
    public static function jsonUnserialize(stdClass $data): EventInterface
    {
        return new GenericErrorEvent(
            $data->message
        );
    }

}
