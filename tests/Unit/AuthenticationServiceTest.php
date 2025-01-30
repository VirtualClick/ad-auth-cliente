<?php

namespace VirtualClick\AdAuthClient\Tests\Unit;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use VirtualClick\AdAuthClient\Exceptions\AuthenticationException;
use VirtualClick\AdAuthClient\Services\AuthenticationResponseService;
use VirtualClick\AdAuthClient\Services\AuthenticationService;
use VirtualClick\AdAuthClient\Tests\TestCase;

class AuthenticationServiceTest extends TestCase
{
    protected $service;
    protected $mockHandler;
    protected $responseService;

    protected function setUp(): void
    {
        parent::setUp();

        // Cria o mock do handler do Guzzle
        $this->mockHandler = new MockHandler();
        $handlerStack = HandlerStack::create($this->mockHandler);
        $client = new Client(['handler' => $handlerStack]);

        // Mock do ResponseService
        $this->responseService = $this->createMock(AuthenticationResponseService::class);

        // Cria o serviço
        $this->service = new AuthenticationService($this->responseService);

        // Injeta o client mockado usando Reflection
        $reflection = new \ReflectionClass($this->service);
        $clientProperty = $reflection->getProperty('client');
        $clientProperty->setAccessible(true);
        $clientProperty->setValue($this->service, $client);
    }

    public function test_successful_authentication()
    {
        // Prepara o mock do ResponseService
        $expectedResponse = ['data' => 'validated_response'];
        $this->responseService->expects($this->once())
            ->method('validate')
            ->willReturn($expectedResponse);

        // Prepara o mock do Guzzle
        $this->mockHandler->append(
            new Response(200, [], json_encode(['raw' => 'response']))
        );

        // Executa o teste
        $result = $this->service->authenticate([
            'authKey' => 'test@example.com',
            'authPass' => 'password123',
        ]);

        // Verifica o resultado
        $this->assertEquals($expectedResponse, $result);
    }

    public function test_throws_exception_on_non_200_response()
    {
        $this->mockHandler->append(
            new Response(500, [], 'Falha na autênticação')
        );

        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage('Falha na autênticação');

        $this->service->authenticate([
            'authKey' => 'test@example.com',
            'authPass' => 'password123',
        ]);
    }

    public function test_throws_exception_on_guzzle_error()
    {
        $this->mockHandler->append(
            new RequestException(
                'Network error',
                new Request('POST', 'test'),
                new Response(500)
            )
        );

        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage('Falha na requisição: Network error');

        $this->service->authenticate([
            'authKey' => 'test@example.com',
            'authPass' => 'password123',
        ]);
    }

    public function test_sends_correct_request_payload()
    {
        // Prepara o mock do ResponseService
        $this->responseService->expects($this->once())
            ->method('validate')
            ->willReturn(['data' => 'response']);

        // Prepara uma resposta vazia
        $this->mockHandler->append(
            new Response(200, [], json_encode(['raw' => 'response']))
        );

        // Dados de teste
        $credentials = [
            'authKey' => 'test@example.com',
            'authPass' => 'password123',
        ];

        // Executa a autenticação
        $this->service->authenticate($credentials);

        // Pega a última requisição feita
        $request = $this->mockHandler->getLastRequest();

        // Verifica o método e o corpo da requisição
        $this->assertEquals('POST', $request->getMethod());
        $this->assertEquals(
            json_encode($credentials),
            $request->getBody()->getContents()
        );
    }
}
