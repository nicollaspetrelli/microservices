<?php

declare(strict_types=1);

namespace Vcampitelli\Framework\Core\Infrastructure\Persistence\User;

use Vcampitelli\Framework\Core\Domain\User\User;
use Vcampitelli\Framework\Core\Infrastructure\Persistence\AbstractPdoRepository;

class UserRepository /* extends AbstractPdoRepository */
{
    /**
     * @var array
     */
    private $users = [
        1 => [
            'id' => 1,
            'username' => 'admin',
            'password' => '$argon2id$v=19$m=65536,t=4,p=1$YVhSS2lYUFJIT3Rxc0d4Lw$jffevEP9VWfuo5aEQg0ABrnfZ7MRK4UFXN8UX9loBq0',
            'roles' => ['admin'],
            'hash' => 'c7ad44cbad762a5da0a452f9e854fdc1e0e7a52a38015f23f3eab1d80b931dd472634dfac71cd34ebc35d16ab7fb8a90c81f975113d6c7538dc69dd8de9077ec',
        ],
        2 => [
            'id' => 2,
            'username' => 'user',
            'password' => '$argon2id$v=19$m=65536,t=4,p=1$RFF4b29rcjV1Mkx0aE1BdQ$Di3HTY2415NPhYdzNfGBQpUXbrO0VnaFlMLQ40z/IKQ',
            'roles' => [],
            'hash' => 'b14361404c078ffd549c03db443c3fede2f3e534d73f78f77301ed97d4a436a9fd9db05ee8b325c0ad36438b43fec8510c204fc1c1edb21d0941c00e9e2c1ce2',
        ],
        3 => [
            'id' => 3,
            'username' => 'moderator',
            'password' => '$argon2id$v=19$m=65536,t=4,p=1$ZEdkYU9NVFdvNzFnRzI3SA$qf+FZQKfttG7LzWsBxplYLEKrlZv+snMZuHu6QxXPGc',
            'roles' => ['moderator'],
            'hash' => 'bcffa7ccb4609495c0175968894c0c3e665a7299149a53e8028bbdf8d631e59b17094c4f91c15f2a34a9621013c7fb18e89de6f67a8c88a06ce639f351d6acb9',
        ],
    ];

    /**
     * Logging in by the specified <username, password> pair
     *
     * @param string $username
     * @param string $password
     *
     * @return User|null
     */
    public function loginByUsernameAndPassword(string $username, string $password): ?User
    {
        foreach ($this->users as $user) {
            if ($user['username'] === $username) {
                if (\password_verify($password, $user['password'])) {
                    return $this->loginById($user['id'], $user['username']);
                }
                break;
            }
        }

        return null;
    }

    /**
     * Logging in by token
     *
     * @param string $token
     * @return User|null
     */
    public function loginByToken(string $token): ?User
    {
        foreach ($this->users as $user) {
            if (\hash_equals($user['hash'], $token)) {
                return $this->loginById($user['id'], $user['username']);
            }
        }
        return null;
    }

    /**
     * Logging in by the user ID
     *
     * @param int $id
     *
     * @return User|null
     */
    public function loginById(int $id): ?User
    {
        if (!isset($this->users[$id])) {
            return null;
        }

        return new User(
            $id,
            $this->users[$id]['username'],
            $this->users[$id]['roles'] ?? []
        );
    }
}
