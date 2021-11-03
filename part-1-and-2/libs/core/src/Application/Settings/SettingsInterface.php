<?php
declare(strict_types=1);

namespace Vcampitelli\Framework\Core\Application\Settings;

interface SettingsInterface
{
    /**
     * @param string $key
     * @param mixed|null $default
     * @return mixed
     */
    public function get(string $key, $default = null);

    /**
     * @param string $key
     * @param mixed $value
     * @return SettingsInterface
     */
    public function set(string $key, $value): SettingsInterface;
}
