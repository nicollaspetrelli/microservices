<?php
declare(strict_types=1);

namespace Reservation\Application\Actions\Reservation;

use Psr\Http\Message\ResponseInterface as Response;

class ViewReservationAction extends AbstractReservationAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $reservationId = (string) $this->resolveArg('id');
        $reservation = $this->reservationRepository->findById($reservationId);

        $this->logger->info("Reservation of id `${reservationId}` was viewed.");

        return $this->respondWithData($reservation);
    }
}
