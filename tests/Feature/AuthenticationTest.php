<?php

namespace VirtualClick\AdAuthClient\Tests\Feature;

use VirtualClick\AdAuthClient\Tests\TestCase;

class AuthenticationTest extends TestCase
{
    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * @return void
     */
    public function testAuthenticateNotAllowed(): void
    {
        $this->assertTrue(true);
    }
}
