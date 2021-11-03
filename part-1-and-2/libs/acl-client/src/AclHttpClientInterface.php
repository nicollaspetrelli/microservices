<?php

declare(strict_types=1);

namespace Vcampitelli\Framework\Acl;

interface AclHttpClientInterface extends AclClientInterface
{
    /**
     * Sets the access token
     *
     * @param string $token
     *
     * @return AclHttpClientInterface
     */
    public function setAccessToken(string $token): AclHttpClientInterface;
}
