<?php

declare(strict_types=1);

namespace Framework\Domain\Event;

use stdClass;

abstract class AbstractEvent implements EventInterface
{
    /**
     * @var stdClass
     */
    private \stdClass $data;

    /**
     * @var string|null
     */
    private ?string $eventId;

    /**
     * @param string $key
     * @param $value
     * @return $this
     */
    protected function set(string $key, $value): self
    {
        if (!isset($this->data)) {
            $this->data = new stdClass;
        }
        $this->data->$key = $value;
        return $this;
    }

    /**
     * @param string $key
     * @param null $default
     * @return mixed
     */
    protected function getOptional(string $key, $default = null): mixed
    {
        if (!isset($this->data)) {
            return $default;
        }
        return $this->data->$key ?? $default;
    }

    /**
     * @param string $key
     * @return mixed
     */
    protected function get(string $key): mixed
    {
        if ((isset($this->data)) && (isset($this->data->$key))) {
            return $this->data->$key;
        }

        throw new \RuntimeException("Invalid property: {$key}");
    }

    /**
     * @return stdClass
     */
    public function jsonSerialize(): stdClass
    {
        if (!isset($this->data)) {
            $this->data = new stdClass;
        }
        return $this->data;
    }

    /**
     * @return string|null
     */
    public function getEventId(): ?string
    {
        return $this->eventId ?? null;
    }

    /**
     * @param stdClass $data
     * @param string $eventId
     *
     * @return EventInterface
     * @throws \ReflectionException
     */
    public static function jsonUnserialize(stdClass $data, string $eventId): EventInterface
    {
        return self::buildFromReflection(
            static::class,
            $data,
            $eventId
        );
    }

    /**
     * @param string $class
     * @param stdClass $data
     * @param string|null $eventId
     * @return object
     * @throws \ReflectionException
     */
    protected static function buildFromReflection(string $class, stdClass $data, string $eventId = null): object
    {
        $reflection = new \ReflectionClass($class);

        $args = [];
        foreach ($reflection->getConstructor()->getParameters() as $parameter) {
            $name = $parameter->getName();
            $type = $parameter->getType();

            if ($type->isBuiltin()) {
                $args[] = $data->$name;
                continue;
            }

            $parameterClass = $type->getName();
            if (!isset($data->$name)) {
                throw new \RuntimeException("Missing {$class} constructor argument: {$name}");
            }
            $args[] = self::buildFromReflection($parameterClass, $data->$name);
        }

        $instance = $reflection->newInstanceArgs($args);
        if (\is_subclass_of($class, AbstractEvent::class)) {
            $instance->eventId = $eventId;
        }
        return $instance;
    }

}
