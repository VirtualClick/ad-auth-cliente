<?php

namespace VirtualClick\AdAuthClient\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use VirtualClick\AdAuthClient\AdAuthServiceProvider;

abstract class TestCase extends Orchestra
{
    /**
     * @param $app
     *
     * @return array
     */
    protected function getPackageProviders($app): array
    {
        return [
            AdAuthServiceProvider::class,
        ];
    }
}
