<?php
declare(strict_types=1);

namespace Reservation\Application\Actions\Reservation;

use Psr\Http\Message\ResponseInterface as Response;

class ListReservationsAction extends AbstractReservationAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $reservations = \array_values($this->reservationRepository->findAll());

        $this->logger->info('Listing all reservations');

        return $this->respondWithData($reservations);
    }
}
