<?php

declare(strict_types=1);

namespace Framework\Domain\Model;

abstract class AbstractWriteModel implements WriteModelInterface
{
    /**
     * @var string|null
     */
    private ?string $id;

    /**
     * @param  string|null  $id
     */
    public function __construct(?string $id)
    {
        $this->setId($id);
    }

    /**
     * Sets the user ID
     *
     * @param  string|null  $id
     *
     * @return WriteModelInterface
     */
    public function setId(?string $id): WriteModelInterface
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * Returns values that should be persisted
     *
     * @return array
     */
    public function getPersistValues(): array
    {
        return $this->jsonSerialize();
    }
}
