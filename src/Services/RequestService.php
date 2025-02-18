<?php

namespace VirtualClick\AdAuthClient\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Response;
use VirtualClick\AdAuthClient\Contracts\ServiceInterface;
use VirtualClick\AdAuthClient\Exceptions\ForbiddenException;
use VirtualClick\AdAuthClient\Exceptions\RequestException;

class RequestService implements ServiceInterface
{
    /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var Client
     */
    protected $client;

    /**
     * @param string $username
     * @param string $password
     */
    public function __construct(
        string $username,
        string $password
    ) {
        $this->username = $username;
        $this->password = $password;

        $this->client = new Client([
            'base_uri' => config('ad-auth.base_url'),
            'timeout' => config('ad-auth.timeout', 30),
        ]);
    }

    /**
     * @return Response
     *
     * @throws ForbiddenException
     * @throws RequestException
     */
    public function __invoke(): Response
    {
        $payload = [
            'authKey' => $this->username,
            'authPass' => $this->password,
            'authKeyType' => config('ad-auth.auth_key_type'),
            'siglaAplicacao' => config('ad-auth.application_code'),
        ];

        try {
            return $this->client->post('', [
                'json' => $payload,
            ]);
        } catch (ClientException | GuzzleException $e) {
            if ($e->getCode() === 403) {
                throw new ForbiddenException();
            }
            throw new RequestException();
        }
    }
}