<?php

namespace VirtualClick\AdAuthClient\Exceptions;

class ForbiddenException extends AuthenticationException
{
    /**
     * @var string
     */
    protected $message = 'Acesso não permitido';

    /**
     * @var int
     */
    protected $code = 403;
}
