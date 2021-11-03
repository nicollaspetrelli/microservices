<?php
declare(strict_types=1);

use Slim\App;
use Framework\Application\Middleware\SessionMiddleware;

return function (App $app) {
    $app->add(SessionMiddleware::class);
};
