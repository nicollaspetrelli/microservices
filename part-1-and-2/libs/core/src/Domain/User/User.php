<?php

declare(strict_types=1);

namespace Vcampitelli\Framework\Core\Domain\User;

class User
{
    /**
     * @var User
     */
    private static User $instance;

    /**
     * @var int
     */
    private int $id;

    /**
     * @var string
     */
    private string $username;

    /**
     * @var string
     */
    private array $roles = [];

    /**
     * User constructor.
     *
     * @param int|null $id
     * @param string|null $username
     * @param array $roles
     */
    public function __construct(?int $id = null, ?string $username = null, array $roles = [])
    {
        if ($id) {
            $this->id = $id;

            // Registering this instance to the singleton
            if (!isset(self::$instance)) {
                self::$instance = $this;
            }
        }

        if (!empty($username)) {
            $this->username = $username;
        }

        if (!empty($roles)) {
            $this->roles = $roles;
        }
    }

    /**
     * Returns the user ID
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Returns the username
     *
     * @return string|null
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * Returns the role for the current user
     *
     * @return array
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @return string|null
     */
    public function getAccessToken(): ?string
    {
        return \hash('sha512', $this->getUsername());
    }

    /**
     * @return \Vcampitelli\Framework\Core\Domain\User\User
     */
    public static function getInstance(): User
    {
        if (!isset(self::$instance)) {
            self::$instance = new User();
        }
        return self::$instance;
    }
}
