<?php
declare(strict_types=1);

use Grades\Application\Actions\Grade\ListGradesAction;
use Grades\Application\Actions\Grade\CreateGradeAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {
    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write('Hello World from Grades System');
        return $response;
    });

    $app->group('/grades', function (Group $group) {
        $group->get('', ListGradesAction::class);
        $group->put('/application/{application:\d+}/criteria/{criteria:\d+}', CreateGradeAction::class);
    });
};
