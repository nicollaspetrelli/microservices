<?php

declare(strict_types=1);

namespace Flight\Domain;

use Framework\Core\Domain\AbstractTransactionalModel;
use Framework\Core\Domain\ModelInterface;
use Reservation\Domain\Reservation;

class Flight extends AbstractTransactionalModel
{

    /**
     * @param string|null        $id
     * @param string             $reservationId
     * @param \DateTimeImmutable $dateStart
     * @param \DateTimeImmutable $dateEnd
     */
    public function __construct(
        private ?string $id,
        private string $reservationId,
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
     * @return string
     */
    public function getReservationId(): string
    {
        return $this->reservationId;
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
            'reservation_id' => $this->reservationId,
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
        return new Flight(
            $data['id'],
            $data['reservation_id'],
            new \DateTimeImmutable($data['start']),
            new \DateTimeImmutable($data['end'])
        );
    }

    /**
     * @param \Reservation\Domain\Reservation $reservation
     *
     * @return \Flight\Domain\Flight
     */
    public static function fromReservation(Reservation $reservation): Flight
    {
        return new Flight(
            null,
            $reservation->getId(),
            $reservation->getDateStart(),
            $reservation->getDateEnd()
        );
    }

}
