<?php

declare(strict_types=1);

namespace Framework\Domain\Model;

use Framework\Domain\Event\EventInterface;
use JsonSerializable;
use stdClass;

interface ModelAggregateInterface extends JsonSerializable
{
    /**
     * Sets the user ID
     *
     * @param string $id
     *
     * @return ModelAggregateInterface
     */
    public function setId(string $id): ModelAggregateInterface;

    /**
     * @return string|null
     */
    public function getId(): ?string;

    /**
     * Applies an event
     *
     * @param  EventInterface  $event
     *
     * @return ModelAggregateInterface
     */
    public function apply(EventInterface $event): ModelAggregateInterface;

}
