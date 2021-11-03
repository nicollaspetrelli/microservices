<?php

declare(strict_types=1);

namespace Framework\Domain\Model;

use JsonSerializable;

interface WriteModelInterface extends JsonSerializable
{
    /**
     * Sets the user ID
     *
     * @param string $id
     *
     * @return WriteModelInterface
     */
    public function setId(string $id): WriteModelInterface;

    /**
     * @return string|null
     */
    public function getId(): ?string;

    /**
     * Returns values that should be persisted
     *
     * @return array
     */
    public function getPersistValues(): array;
}
