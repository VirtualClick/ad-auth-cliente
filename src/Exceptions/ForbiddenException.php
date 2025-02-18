<?php

namespace VirtualClick\AdAuthClient\Exceptions;

use Exception;

class ForbiddenException extends Exception
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
