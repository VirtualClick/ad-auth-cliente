<?php

namespace VirtualClick\AdAuthClient\Contracts;

interface RuleInterface
{
    /**
     * @param array $response
     *
     * @return void
     */
    public function validate(array $response): void;
}
