<?php

namespace VirtualClick\AdAuthClient\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use VirtualClick\AdAuthClient\Contracts\AuthenticationInterface;
use VirtualClick\AdAuthClient\Exceptions\AuthenticationException;

class AuthenticationService implements AuthenticationInterface
{
    protected $client;

    protected $responseService;

    protected $baseUrl;

    /**
     * @param AuthenticationResponseService $responseService
     */
    public function __construct(AuthenticationResponseService $responseService)
    {
        $this->responseService = $responseService;
        $this->baseUrl = config('ad-auth.base_url');
        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'timeout' => config('ad-auth.timeout', 30),
        ]);
    }

    /**
     * @param array $credentials
     *
     * @return array
     *
     * @throws AuthenticationException
     */
    public function authenticate(array $credentials): array
    {
        if (! isset($credentials['authKey']) || ! isset($credentials['authPass'])) {
            throw new AuthenticationException('Credenciais incompletas');
        }

        $payload = [
            'authKey' => $credentials['authKey'],
            'authPass' => $credentials['authPass'],
            'authKeyType' => config('ad-auth.auth_key_type'),
            'siglaAplicacao' => config('ad-auth.application_code'),
        ];

        try {
            $response = $this->client->post('', [
                'json' => $payload,
            ]);
            $responseData = json_decode($response->getBody()->getContents(), true);

            return $this->responseService->handleResponse($responseData);
        } catch (GuzzleException $e) {
            throw new AuthenticationException('Falha na requisição: ' . $e->getMessage());
        }
    }
}
