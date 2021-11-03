<?php
declare(strict_types=1);

namespace Vcampitelli\Framework\Core\Application\Settings;

class Settings implements SettingsInterface
{
    /**
     * @var array
     */
    private $settings;

    /**
     * Settings constructor.
     * @param array $settings
     */
    public function __construct(array $settings)
    {
        $this->settings = $settings;
    }

    /**
     * @param string $key
     * @param mixed|null $default
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        return $this->settings[$key] ?? $default;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return SettingsInterface
     */
    public function set(string $key, $value): SettingsInterface
    {
        $this->settings[$key] = $value;
        return $this;
    }
}
