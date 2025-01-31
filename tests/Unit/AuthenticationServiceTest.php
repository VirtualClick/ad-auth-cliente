<?php

namespace VirtualClick\AdAuthClient\Tests\Unit;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Mockery;
use ReflectionClass;
use ReflectionException;
use VirtualClick\AdAuthClient\Exceptions\AuthenticationException;
use VirtualClick\AdAuthClient\Services\AuthenticationResponseService;
use VirtualClick\AdAuthClient\Services\AuthenticationService;
use VirtualClick\AdAuthClient\Tests\TestCase;

class AuthenticationServiceTest extends TestCase
{
    protected $service;

    protected $mockHandler;

    protected $responseService;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->mockHandler = new MockHandler();
        $handlerStack = HandlerStack::create($this->mockHandler);
        $client = new Client(['handler' => $handlerStack]);

        $this->responseService = $this->createMock(AuthenticationResponseService::class);

        $this->service = new AuthenticationService($this->responseService);

        $reflection = new \ReflectionClass($this->service);
        $clientProperty = $reflection->getProperty('client');
        $clientProperty->setAccessible(true);
        $clientProperty->setValue($this->service, $client);
    }

    /**
     * @return void
     *
     * @throws ReflectionException
     */
    public function test_constructor_sets_correct_config(): void
    {
        config(['ad-auth.base_url' => 'http://test-api.com']);
        config(['ad-auth.timeout' => 60]);

        $service = new AuthenticationService($this->responseService);

        $reflection = new ReflectionClass($service);

        $baseUrlProperty = $reflection->getProperty('baseUrl');
        $baseUrlProperty->setAccessible(true);

        $clientProperty = $reflection->getProperty('client');
        $clientProperty->setAccessible(true);

        $this->assertEquals('http://test-api.com', $baseUrlProperty->getValue($service));

        $client = $clientProperty->getValue($service);
        $this->assertInstanceOf(Client::class, $client);

        $clientReflection = new \ReflectionClass($client);
        $configProperty = $clientReflection->getProperty('config');
        $configProperty->setAccessible(true);
        $config = $configProperty->getValue($client);

        $this->assertEquals('http://test-api.com', $config['base_uri']);
        $this->assertEquals(60, $config['timeout']);
    }

    /**
     * @return void
     *
     * @throws AuthenticationException
     */
    public function test_successful_authentication(): void
    {
        $expectedResponse = ['data' => 'validated_response'];
        $this->responseService->expects($this->once())
            ->method('validate')
            ->willReturn($expectedResponse);

        $this->mockHandler->append(
            new Response(200, [], json_encode(['raw' => 'response']))
        );

        $result = $this->service->authenticate([
            'authKey' => 'test@example.com',
            'authPass' => 'password123',
        ]);

        $this->assertEquals($expectedResponse, $result);
    }

    /**
     * @return void
     *
     * @throws AuthenticationException
     */
    public function test_throws_exception_on_guzzle_error(): void
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

    /**
     * @return void
     *
     * @throws AuthenticationException
     */
    public function test_sends_correct_request_payload(): void
    {
        $this->responseService->expects($this->once())
            ->method('validate')
            ->willReturn(['data' => 'response']);

        $this->mockHandler->append(
            new Response(200, [], json_encode(['raw' => 'response']))
        );

        $credentials = [
            'authKey' => 'test@example.com',
            'authPass' => 'password123',
        ];

        $this->service->authenticate($credentials);

        $request = $this->mockHandler->getLastRequest();

        $this->assertEquals('POST', $request->getMethod());
        $this->assertEquals(
            json_encode($credentials),
            $request->getBody()->getContents()
        );
    }
}
