<?php

declare(strict_types=1);

namespace Reservation\Application\Actions\Reservation;

use Psr\Http\Message\ResponseInterface as Response;
use Reservation\Domain\Events\PendingReservationCreatedEvent;
use Reservation\Domain\Reservation;
use Slim\Exception\HttpBadRequestException;

class CreateReservationAction extends AbstractReservationAction
{

    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $data = $this->request->getParsedBody();
        if (empty($data['start'])) {
            throw new HttpBadRequestException($this->request, 'Missing start date field');
        }

        if (empty($data['end'])) {
            throw new HttpBadRequestException($this->request, 'Missing end date field');
        }

        try {
            $data['start'] = new \DateTimeImmutable($data['start']);
        } catch (\Throwable $throwable) {
            throw new HttpBadRequestException($this->request, $throwable->getMessage());
        }

        try {
            $data['end'] = new \DateTimeImmutable($data['end']);
        } catch (\Throwable $throwable) {
            throw new HttpBadRequestException($this->request, $throwable->getMessage());
        }

        $reservation = new Reservation(
            null,
            $data['start'],
            $data['end']
        );
        $this->reservationRepository->save($reservation);

        $this->publishSuccessEvent(
            new PendingReservationCreatedEvent(
                $reservation
            )
        );

        $this->logger->info(
            \sprintf(
                'Starting to create reservation %s (%s - %s)',
                $reservation->getId(),
                $reservation->getDateStart()->format(\DateTimeInterface::RFC3339),
                $reservation->getDateEnd()->format(\DateTimeInterface::RFC3339)
            )
        );

        return $this->respondWithData($reservation);
    }

}
