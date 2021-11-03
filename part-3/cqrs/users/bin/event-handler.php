<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

Framework\EventHandler::run(
  __DIR__,
  ($_ENV['ENVIRONMENT'] ?? '') === 'production'
);
