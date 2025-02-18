<?php

namespace VirtualClick\AdAuthClient\Exceptions;

use Exception;

class RequestException extends Exception
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
