<?php

namespace VirtualClick\AdAuthClient\Facades;

use Illuminate\Support\Facades\Facade;

class AdAuth extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'ad-auth';
    }
}
