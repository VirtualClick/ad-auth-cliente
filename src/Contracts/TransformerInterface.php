<?php

namespace VirtualClick\AdAuthClient\Contracts;

interface TransformerInterface
{
    /**
     * @param array $response
     *
     * @return array
     */
    public static function hanler(array $response): array;
}
