<?php

namespace VirtualClick\AdAuthClient\Exceptions;

class SystemNotAllowedRuleException extends RuleException
{
    /**
     * @var int
     */
    protected $code = 401;
}
