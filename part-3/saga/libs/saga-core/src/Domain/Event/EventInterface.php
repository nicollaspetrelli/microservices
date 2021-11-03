<?php

declare(strict_types=1);

namespace Framework\Core\Domain\Event;

use stdClass;

interface EventInterface extends \JsonSerializable
{

    /**
     * @return string
     */
    public function getUuid(): string;

    /**
     * @param stdClass $data
     *
     * @return \Framework\Core\Domain\Event\EventInterface
     */
    public static function jsonUnserialize(stdClass $data): EventInterface;

}
