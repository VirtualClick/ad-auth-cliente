<?php

namespace VirtualClick\AdAuthClient\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;

class GuzzleTest extends TestCase
{
    /**
     * @throws GuzzleException
     */
    public function testConnection()
    {
        $client = new Client([
//            'base_uri' => 'https://www33.hermespardini.com.br/ihpsecurityws/autenticarusuario',
            'base_uri' => 'https://hermespardini.com.br/ihpsecurityws/autenticarusuario',
            'timeout' => 30,
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
        ]);

        try {
            $response = $client->post('', [
                'json' => [
                    'authKey' => 'sergio.hess',
                    'authPass' => 'pT6#v8mshQIwnqSjPc$sRmr0iLs$YgZ!',
                    'authKeyType' => 7,
                    'siglaAplicacao' => 'FENIX',
                ],
            ]);

            dd(json_decode($response->getBody()->getContents(), true)['status']);
        } catch (ClientException $e) {
            dd($e->getCode());
        }
    }
}