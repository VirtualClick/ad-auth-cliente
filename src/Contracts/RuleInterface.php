<?php

namespace VirtualClick\AdAuthClient\Contracts;

use GuzzleHttp\Psr7\Response;
use VirtualClick\AdAuthClient\Exceptions\RuleException;

interface RuleInterface
{
    /**
     * @param Response $response
     *
     * @return void
     *
     * @throws RuleException
     */
    public function validate(Response $response): void;
}
