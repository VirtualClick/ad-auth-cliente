<?php

namespace VirtualClick\AdAuthClient\Tests\Feature\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Illuminate\Contracts\Container\BindingResolutionException;
use ReflectionClass;
use ReflectionException;
use VirtualClick\AdAuthClient\Exceptions\ForbiddenException;
use VirtualClick\AdAuthClient\Exceptions\RequestException;
use VirtualClick\AdAuthClient\Services\RequestService;
use VirtualClick\AdAuthClient\Tests\TestCase;

class RequestServiceTest extends TestCase
{
    /**
     * @return void
     */
    public function testConstructor(): void
    {
        $testUsername = 'test_username';
        $testPassword = 'test_password';

        config([
            'ad-auth.base_url' => 'https://api.example.com/',
            'ad-auth.timeout' => 45,
        ]);

        $service = new RequestService($testUsername, $testPassword);

        $reflection = new ReflectionClass($service);

        $usernameProperty = $reflection->getProperty('username');
        $usernameProperty->setAccessible(true);
        $this->assertEquals($testUsername, $usernameProperty->getValue($service));

        $passwordProperty = $reflection->getProperty('password');
        $passwordProperty->setAccessible(true);
        $this->assertEquals($testPassword, $passwordProperty->getValue($service));

        $clientProperty = $reflection->getProperty('client');
        $clientProperty->setAccessible(true);
        $client = $clientProperty->getValue($service);

        $this->assertInstanceOf(Client::class, $client);
    }

    /**
     * @return void
     *
     * @throws BindingResolutionException
     * @throws ForbiddenException
     * @throws RequestException
     * @throws ReflectionException
     */
    public function testResponseNotAllowed(): void
    {
        $mock = new MockHandler([
            new Response(
                200,
                [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
                json_encode($this->getResponseNotAllowed())
            ),
        ]);
        $handlerStack = HandlerStack::create($mock);
        $mockClient = new Client(['handler' => $handlerStack]);

        $service = app()->make(RequestService::class, [
            'username' => fake()->userName(),
            'password' => fake()->password(),
        ]);

        $reflection = new ReflectionClass($service);
        $clientProperty = $reflection->getProperty('client');
        $clientProperty->setAccessible(true);
        $clientProperty->setValue($service, $mockClient);

        $response = $service();
        $responseData = json_decode($response->getBody()->getContents(), true);

        $this->assertEquals('NOK', $responseData['status']);
        $this->assertEquals('Usuário sem permissão de acesso.', $responseData['mensagem']);
        $this->assertEquals('AAAAAAAAA', $responseData['perfilUsuario']['siglaAplicacao']);
    }

    /**
     * @return void
     *
     * @throws BindingResolutionException
     * @throws ForbiddenException
     * @throws RequestException
     * @throws ReflectionException
     */
    public function testSystemResponseNotAllowed(): void
    {
        $mock = new MockHandler([
            new Response(
                200,
                [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
                json_encode($this->getResponseSystemNotAllowed())
            ),
        ]);
        $handlerStack = HandlerStack::create($mock);
        $mockClient = new Client(['handler' => $handlerStack]);

        $service = app()->make(RequestService::class, [
            'username' => fake()->userName(),
            'password' => fake()->password(),
        ]);

        $reflection = new ReflectionClass($service);
        $clientProperty = $reflection->getProperty('client');
        $clientProperty->setAccessible(true);
        $clientProperty->setValue($service, $mockClient);

        $response = $service();
        $responseData = json_decode($response->getBody()->getContents(), true);

        $this->assertEquals('NOK', $responseData['status']);
        $this->assertEquals('S', $responseData['usuarioAtivo']);
        $this->assertEquals('Usuário sem permissão de acesso.', $responseData['mensagem']);
        $this->assertEquals('AAAAAAAAA', $responseData['perfilUsuario']['siglaAplicacao']);
        $this->assertEquals('12345678909', $responseData['perfilUsuario']['idUsuario']);
        $this->assertEquals('AAAA', $responseData['perfilUsuario']['siglaUsuario']);
    }

    /**
     * @return void
     *
     * @throws BindingResolutionException
     * @throws ForbiddenException
     * @throws RequestException
     * @throws ReflectionException
     */
    public function testADUserData(): void
    {
        $mock = new MockHandler([
            new Response(
                200,
                [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
                json_encode($this->getResponseADUser())
            ),
        ]);
        $handlerStack = HandlerStack::create($mock);
        $mockClient = new Client(['handler' => $handlerStack]);

        $service = app()->make(RequestService::class, [
            'username' => fake()->userName(),
            'password' => fake()->password(),
        ]);

        $reflection = new ReflectionClass($service);
        $clientProperty = $reflection->getProperty('client');
        $clientProperty->setAccessible(true);
        $clientProperty->setValue($service, $mockClient);

        $response = $service();
        $responseData = json_decode($response->getBody()->getContents(), true);

        $this->assertEquals('OK', $responseData['status']);
        $this->assertEquals('Usuário encontrado.', $responseData['mensagem']);
        $this->assertEquals('Aaaaaa', $responseData['usuarioAd']['nome']);
        $this->assertEquals('aaaaaa.aaaaaaa@aaaaaaaa.com', $responseData['usuarioAd']['email']);
        $this->assertEquals(12345678909, $responseData['perfilUsuario']['idUsuario']);
    }

    /**
     * @return void
     *
     * @throws BindingResolutionException
     * @throws ForbiddenException
     * @throws ReflectionException
     * @throws RequestException
     */
    public function testForbiddenException(): void
    {
        $request = new Request('POST', '');
        $response = new Response(
            403,
            ['Content-Type' => 'application/json'],
            json_encode($this->getResponseNotAllowed())
        );

        $exception = new ClientException(
            'Forbidden',
            $request,
            $response
        );

        $mock = new MockHandler([
            $exception,
        ]);

        $handlerStack = HandlerStack::create($mock);
        $mockClient = new Client(['handler' => $handlerStack]);

        $service = app()->make(RequestService::class, [
            'username' => fake()->userName(),
            'password' => fake()->password(),
        ]);

        $reflection = new ReflectionClass($service);
        $clientProperty = $reflection->getProperty('client');
        $clientProperty->setAccessible(true);
        $clientProperty->setValue($service, $mockClient);

        $this->expectException(ForbiddenException::class);

        $service();
    }

    /**
     * @return void
     *
     * @throws ForbiddenException
     * @throws RequestException
     */
    public function testGenericRequestException(): void
    {
        $request = new Request('POST', '');

        $response = new Response(
            500,
            ['Content-Type' => 'application/json'],
            json_encode(['error' => 'Internal Server Error'])
        );

        $exception = new ClientException(
            'Server Error',
            $request,
            $response
        );

        $mock = new MockHandler([
            $exception,
        ]);

        $handlerStack = HandlerStack::create($mock);
        $mockClient = new Client(['handler' => $handlerStack]);

        $service = new RequestService('test_username', 'test_password');

        $reflection = new ReflectionClass($service);
        $clientProperty = $reflection->getProperty('client');
        $clientProperty->setAccessible(true);
        $clientProperty->setValue($service, $mockClient);

        $this->expectException(RequestException::class);

        $service();
    }
}
