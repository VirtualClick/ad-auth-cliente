<?php

namespace Rules;

use VirtualClick\AdAuthClient\Exceptions\NotAllowedRuleException;
use VirtualClick\AdAuthClient\Rules\NotAllowedRule;
use VirtualClick\AdAuthClient\Tests\TestCase;

class NotAllowedRuleTest extends TestCase
{
    /**
     * @return void
     */
    public function testExpectNotAllowedRuleException(): void
    {
        $rule = new NotAllowedRule();

        $this->expectException(NotAllowedRuleException::class);

        $rule->validate($this->getResponseNotAllowed());
    }
}
