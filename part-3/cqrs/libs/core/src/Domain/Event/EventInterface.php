<?php

declare(strict_types=1);

namespace Framework\Domain\Event;

use stdClass;

interface EventInterface extends \JsonSerializable
{

    /**
     * @param stdClass $data
     *
     * @return \Framework\Domain\Event\EventInterface
     */
    public static function jsonUnserialize(stdClass $data): EventInterface;

}
