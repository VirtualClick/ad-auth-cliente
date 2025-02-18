<?php

namespace VirtualClick\AdAuthClient\Exceptions;

class NotAllowedRuleException extends RuleException
{
    /**
     * @var int
     */
    protected $code = 401;
}
