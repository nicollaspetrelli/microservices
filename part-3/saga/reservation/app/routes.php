<?php
declare(strict_types=1);

use Reservation\Application\Actions\Reservation\CreateReservationAction;
use Reservation\Application\Actions\Reservation\ListReservationsAction;
use Reservation\Application\Actions\Reservation\ViewReservationAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {
    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });

    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write('Hello world!');
        return $response;
    });

    $app->group('/reservations', function (Group $group) {
        $group->post('', CreateReservationAction::class);
        $group->get('', ListReservationsAction::class);
        $group->get('/{id}', ViewReservationAction::class);
    });
};
