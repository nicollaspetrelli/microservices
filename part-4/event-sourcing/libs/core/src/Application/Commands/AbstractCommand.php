<?php

declare(strict_types=1);

namespace Framework\Application\Commands;

use Charm\Id;
use ReflectionException;
use stdClass;

abstract class AbstractCommand implements CommandInterface
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
     * @return CommandInterface
     * @throws ReflectionException
     */
    public static function jsonUnserialize(stdClass $data): CommandInterface
    {
        if ((empty($data->uuid)) || (!isset($data->uuid))) {
            throw new \InvalidArgumentException('Bad data provided');
        }

        $command = self::buildCommandFromData($data);
        $command->uuid = $data->uuid;

        return $command;
    }

    /**
     * @param  stdClass  $data
     *
     * @return AbstractCommand
     * @throws ReflectionException
     */
    private static function buildCommandFromData(stdClass $data): AbstractCommand
    {
        if (empty($data->data)) {
            return new static();
        }

        $class = new \ReflectionClass(\get_called_class());
        $args = [];
        foreach ($class->getConstructor()->getParameters() as $parameter) {
            $name = $parameter->getName();
            $args[] = $data->data->$name;
        }
        return $class->newInstanceArgs($args);
    }
}
