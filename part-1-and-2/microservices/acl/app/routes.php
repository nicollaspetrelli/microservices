<?php
declare(strict_types=1);

use App\AclInterface;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Vcampitelli\Framework\Core\Domain\User\User;

return function (App $app) {
    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write('Hello World from ACL!');
        return $response;
    });

    $app->get('/acl/role/{role}/resources', function (Request $request, Response $response, array $args) {
        $roles = $this->get(User::class)->getRoles();
        if (!\in_array($args['role'], $roles)) {
            return $response->withStatus(StatusCodeInterface::STATUS_FORBIDDEN);
        }

        $response = $response->withHeader('Content-Type', 'application/json');
        $response->getBody()->write(\json_encode([
            'resources' => $this->get(AclInterface::class)->getAllowedResources($args['role']),
        ]));
        return $response;
    });
};
