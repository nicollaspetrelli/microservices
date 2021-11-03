<?php

declare(strict_types=1);

namespace Reservation\Domain;

use Framework\Core\Domain\AbstractTransactionalModel;
use Framework\Core\Domain\ModelInterface;

/**
 * Class that represents an application
 */
class Reservation extends AbstractTransactionalModel
{

    /**
     * @param string|null $id
     * @param \DateTimeImmutable $dateStart
     * @param \DateTimeImmutable $dateEnd
     */
    public function __construct(
        private ?string $id,
        private \DateTimeImmutable $dateStart,
        private \DateTimeImmutable $dateEnd
    ) {
    }

    /**
     * @param string $id
     *
     * @return ModelInterface
     */
    public function setId(string $id): ModelInterface
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
     * @return \DateTimeImmutable
     */
    public function getDateStart(): \DateTimeImmutable
    {
        return $this->dateStart;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getDateEnd(): \DateTimeImmutable
    {
        return $this->dateEnd;
    }

    /**
     * @return array
     */
    protected function toArrayFromProxy(): array
    {
        return [
            'id' => $this->id,
            'start' => $this->dateStart->format(\DateTimeInterface::RFC3339),
            'end' => $this->dateEnd->format(\DateTimeInterface::RFC3339),
        ];
    }

    /**
     * @param array $data
     * @return ModelInterface
     * @throws \Exception
     */
    protected static function buildFromProxy(array $data): ModelInterface
    {
        return new Reservation(
            $data['id'],
            new \DateTimeImmutable($data['start']),
            new \DateTimeImmutable($data['end'])
        );
    }

}
