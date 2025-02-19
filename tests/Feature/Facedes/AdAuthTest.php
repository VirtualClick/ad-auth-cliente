<?php

namespace Facedes;

use Exception;
use Illuminate\Support\Facades\App;
use Mockery;
use ReflectionClass;
use ReflectionException;
use VirtualClick\AdAuthClient\AdAuthServiceProvider;
use VirtualClick\AdAuthClient\Contracts\AuthenticationInterface;
use VirtualClick\AdAuthClient\Facades\AdAuth;
use VirtualClick\AdAuthClient\Tests\TestCase;

class AdAuthTest extends TestCase
{
    /**
     * @return void
     */
    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * @return void
     *
     * @throws ReflectionException
     */
    public function testFacadeAccessor()
    {
        $this->assertEquals('ad-auth', $this->getAccessor());
    }

    /**
     * @return void
     */
    public function testFacadeResolvesCorrectBinding()
    {
        $mockService = Mockery::mock('AdAuthService');

        App::instance('ad-auth', $mockService);

        $this->assertSame($mockService, App::make('ad-auth'));
    }

    /**
     * @return void
     *
     * @throws Exception
     */
    public function testFacadeProxiesMethodCalls()
    {
        $mockService = Mockery::mock('AdAuthService');
        $mockService->shouldReceive('authenticate')
            ->once()
            ->with('username', 'password')
            ->andReturn(true);

        App::instance('ad-auth', $mockService);

        $result = AdAuth::authenticate('username', 'password');

        $this->assertTrue($result);
    }

    /**
     * @return void
     */
    public function testServiceProviderBindsAuthenticationInterface()
    {
        $app = $this->createApplication();

        $provider = new AdAuthServiceProvider($app);
        $provider->register();

        $mockAuth = Mockery::mock(AuthenticationInterface::class);

        $app->instance(AuthenticationInterface::class, $mockAuth);

        $resolved = $app->make('ad-auth');

        $this->assertSame($mockAuth, $resolved);
        $this->assertInstanceOf(AuthenticationInterface::class, $resolved);
    }

    /**
     * @return mixed
     *
     * @throws ReflectionException
     */
    protected function getAccessor()
    {
        $reflectionClass = new ReflectionClass(AdAuth::class);
        $method = $reflectionClass->getMethod('getFacadeAccessor');
        $method->setAccessible(true);
        return $method->invoke(null);
    }
}
