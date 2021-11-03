<?php

declare(strict_types=1);

namespace Framework\Core\Domain\Event;

use Charm\Id;
use ReflectionException;
use stdClass;

abstract class AbstractEvent implements EventInterface
{

    /**
     * Data bag
     *
     * @var stdClass
     */
    private stdClass $data;

    /**
     * @var string
     */
    private string $uuid;

    /**
     * Sets a value for a field
     *
     * @param  string  $key
     * @param          $value
     *
     * @return $this
     */
    protected function set(string $key, $value): self
    {
        if (!isset($this->data)) {
            $this->data = new stdClass();
        }
        $this->data->$key = $value;
        return $this;
    }

    /**
     * Returns the value for the field
     *
     * @param  string  $key
     * @param  null    $default
     *
     * @return mixed
     */
    protected function get(string $key, $default = null): mixed
    {
        return $this->data->$key ?? $default;
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
            'uuid' => $this->getUuid(),
            'data' => (array) $this->data,
        ];
    }

    /**
     * @param  stdClass  $data
     *
     * @return EventInterface
     * @throws ReflectionException
     */
    public static function jsonUnserialize(stdClass $data): EventInterface
    {
        if ((empty($data->uuid)) || (!isset($data->uuid))) {
            throw new \InvalidArgumentException('Bad data provided');
        }

        $event = self::buildEventFromData($data);
        $event->uuid = $data->uuid;

        return $event;
    }

    /**
     * @param  stdClass  $data
     *
     * @return AbstractEvent
     * @throws ReflectionException
     */
    private static function buildEventFromData(stdClass $data): AbstractEvent
    {
        if (empty($data->data)) {
            return new static();
        }

        $class = new \ReflectionClass(\get_called_class());
        $args = [];
        foreach ($class->getConstructor()->getParameters() as $parameter) {
            $name = $parameter->getName();
            $type = $parameter->getType();

            if ($type->isBuiltin()) {
                $args[] = $data->data->$name;
                continue;
            }

            $parameterClass = $type->getName();
            $args[] = $parameterClass::build((array) $data->data->$name);

        }
        return $class->newInstanceArgs($args);
    }
}
