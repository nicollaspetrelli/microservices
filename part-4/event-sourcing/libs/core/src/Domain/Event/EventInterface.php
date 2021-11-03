<?php

declare(strict_types=1);

namespace Framework\Domain\Event;

use stdClass;

interface EventInterface extends \JsonSerializable
{

    /**
     * @return string|null
     */
    public function getEventId(): ?string;

    /**
     * @param stdClass $data
     * @param string $eventId
     *
     * @return \Framework\Domain\Event\EventInterface
     */
    public static function jsonUnserialize(stdClass $data, string $eventId): EventInterface;

}
