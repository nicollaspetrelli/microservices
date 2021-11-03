<?php

declare(strict_types=1);

namespace Framework\Application\Commands;

use stdClass;

interface CommandInterface extends \JsonSerializable
{

    /**
     * @return string
     */
    public function getUuid(): string;

    /**
     * @param stdClass $data
     *
     * @return CommandInterface
     */
    public static function jsonUnserialize(stdClass $data): CommandInterface;

}
