<?php

declare(strict_types=1);

namespace Framework\Core\Domain;

interface ModelInterface
{
    /**
     * @param string $id
     *
     * @return ModelInterface
     */
    public function setId(string $id): ModelInterface;

    /**
     * @return string|null
     */
    public function getId(): ?string;

    /**
     * @return array
     */
    public function toArray(): array;

    /**
     * @param array $data
     * @return ModelInterface
     */
    public static function build(array $data): ModelInterface;
}
