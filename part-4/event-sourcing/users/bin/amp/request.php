<?php
declare(strict_types=1);

use Amp\Loop;

require __DIR__ . '/../../vendor/autoload.php';

Loop::run(function () {
    try {
        $http = \Amp\Http\Client\HttpClientBuilder::buildDefault();
        $request = new \Amp\Http\Client\Request("https://amphp.org/");
        /** @var \Amp\Http\Client\Response $response */
        $response = yield $http->request($request);

        if ($response->getStatus() !== 200) {
            throw new \Exception($response->getStatus());
        }

        var_dump(get_class($response));
        $body = yield $response->getBody()->buffer();
        var_dump($body);
    } catch (\Exception $e)  {
        var_dump($e->getMessage());
        // handle error
    }
});
