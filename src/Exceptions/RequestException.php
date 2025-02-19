<?php

namespace VirtualClick\AdAuthClient\Exceptions;

class RequestException extends AuthenticationException
{
    /**
     * @var string
     */
    protected $message = 'Falha de request não mapeada';

    /**
     * @var int
     */
    protected $code = 500;
}
