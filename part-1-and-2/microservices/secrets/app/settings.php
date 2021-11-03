<?php
declare(strict_types=1);

use Vcampitelli\Framework\Core\Application\Settings\SettingsInterface;

return function (SettingsInterface $settings) {
    $settings->set('key', $_ENV['APP_SECRET_KEY']);
};
