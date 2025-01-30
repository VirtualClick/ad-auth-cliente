<?php

namespace VirtualClick\AdAuthClient\Contracts;

use VirtualClick\AdAuthClient\Exceptions\AuthenticationException;

interface AuthenticationInterface
{
    /**
     * Authenticate user with provided credentials
     *
     * @param array $credentials
     *
     * @return array
     * @throws AuthenticationException
     */
    public function authenticate(array $credentials): array;
}
