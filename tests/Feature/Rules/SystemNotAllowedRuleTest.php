<?php

namespace Rules;

use VirtualClick\AdAuthClient\Exceptions\SystemNotAllowedRuleException;
use VirtualClick\AdAuthClient\Rules\SystemNotAllowedRule;
use VirtualClick\AdAuthClient\Tests\TestCase;

class SystemNotAllowedRuleTest extends TestCase
{
    /**
     * @return void
     */
    public function testExpectSystemNotAllowedRuleException(): void
    {
        $rule = new SystemNotAllowedRule();

        $this->expectException(SystemNotAllowedRuleException::class);

        $rule->validate($this->getResponseSystemNotAllowed());
    }
}
