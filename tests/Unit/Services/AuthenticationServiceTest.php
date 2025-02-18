<?php

namespace VirtualClick\AdAuthClient\Tests\Unit\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use VirtualClick\AdAuthClient\Exceptions\AuthenticationException;
use VirtualClick\AdAuthClient\Services\AuthenticationResponseService;
use VirtualClick\AdAuthClient\Services\AuthenticationService;
use VirtualClick\AdAuthClient\Tests\TestCase;

class AuthenticationServiceTest extends TestCase
{
    private $authService;

    private $mockHandler;

    protected function setUp(): void
    {
        parent::setUp();

        if (! function_exists('config')) {
            function config($key, $default = null)
            {
                $configs = [
                    'ad-auth.base_url' => 'http://example.com',
                    'ad-auth.timeout' => 30,
                    'ad-auth.auth_key_type' => 'CPF',
                    'ad-auth.application_code' => 'TEST',
                ];
                return $configs[$key] ?? $default;
            }
        }

        $responseService = new AuthenticationResponseService();
        $this->mockHandler = new MockHandler();
        $handlerStack = HandlerStack::create($this->mockHandler);

        $client = new Client(['handler' => $handlerStack]);

        $this->authService = new AuthenticationService($responseService);
        $reflection = new \ReflectionClass($this->authService);
        $clientProperty = $reflection->getProperty('client');
        $clientProperty->setAccessible(true);
        $clientProperty->setValue($this->authService, $client);
    }

    public function testAuthenticateWithValidCredentials()
    {
        $successResponse = [
            'usuarioAtivo' => 'S',
            'status' => 'OK',
            'mensagem' => 'Autenticado com sucesso',
            'perfilUsuario' => [
                'idUsuario' => '12345678900',
                'nomeUsuario' => 'John Doe',
            ],
        ];

        $this->mockHandler->append(
            new Response(200, [], json_encode($successResponse))
        );

        $credentials = [
            'authKey' => '12345678900',
            'authPass' => 'password123',
        ];

        $result = $this->authService->authenticate($credentials);

        $this->assertEquals('12345678900', $result['usuario']['cpf']);
        $this->assertEquals('John Doe', $result['usuario']['nome']);
    }

    public function testAuthenticateWithIncompleteCredentials()
    {
        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage('Credenciais incompletas');

        $credentials = [
            'authKey' => '12345678900',
        ];

        $this->authService->authenticate($credentials);
    }

    public function testAuthenticateWithServerError()
    {
        $this->expectException(AuthenticationException::class);

        $this->mockHandler->append(
            new Response(500, [], 'Internal Server Error')
        );

        $credentials = [
            'authKey' => '12345678900',
            'authPass' => 'password123',
        ];

        $this->authService->authenticate($credentials);
    }
}