<?php

namespace VirtualClick\AdAuthClient\Contracts;

use VirtualClick\AdAuthClient\Exceptions\AuthenticationException;

interface AuthenticationInterface
{
    /**
     * @param string $username
     * @param string $password
     *
     * @return array|null
     *
     * @throws AuthenticationException
     */
    public function authenticate(string $username, string $password): ?array;
}