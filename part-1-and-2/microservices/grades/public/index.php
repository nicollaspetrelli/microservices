<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

Vcampitelli\Framework\Core\Index::run(
    __DIR__,
    ($_ENV['ENVIRONMENT'] ?? '') === 'production'
);
