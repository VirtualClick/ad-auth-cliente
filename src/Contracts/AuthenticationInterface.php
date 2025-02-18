<?php

namespace VirtualClick\AdAuthClient\Contracts;

use Exception;

interface AuthenticationInterface
{
    /**
     * @param string $username
     * @param string $password
     *
     * @return array|null
     *
     * @throws Exception
     */
    public function authenticate(string $username, string $password): ?array;
}