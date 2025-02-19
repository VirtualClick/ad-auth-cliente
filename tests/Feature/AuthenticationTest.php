<?php

namespace VirtualClick\AdAuthClient\Tests\Feature;

use GuzzleHttp\Psr7\Response;
use Illuminate\Contracts\Container\BindingResolutionException;
use Mockery;
use VirtualClick\AdAuthClient\Authentication;
use VirtualClick\AdAuthClient\Exceptions\ForbiddenException;
use VirtualClick\AdAuthClient\Exceptions\NotAllowedRuleException;
use VirtualClick\AdAuthClient\Exceptions\RequestException;
use VirtualClick\AdAuthClient\Exceptions\SystemNotAllowedRuleException;
use VirtualClick\AdAuthClient\Rules\NotAllowedRule;
use VirtualClick\AdAuthClient\Rules\SystemNotAllowedRule;
use VirtualClick\AdAuthClient\Services\RequestService;
use VirtualClick\AdAuthClient\Tests\TestCase;

class AuthenticationTest extends TestCase
{
    /**
     * @var string
     */
    protected $username = 'test_username';

    /**
     * @var string
     */
    protected $password = 'test_password';

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        Mockery::close();
    }

    /**
     * @throws BindingResolutionException
     * @throws ForbiddenException
     * @throws RequestException
     */
    public function testNotAllowed(): void
    {
        $responseContent = json_encode($this->getResponseNotAllowed());

        app()->bind(RequestService::class, function () use ($responseContent) {
            $mockService = Mockery::mock(RequestService::class);
            $mockService->shouldReceive('__invoke')
                ->once()
                ->andReturn(
                    new Response(
                        200,
                        ['Content-Type' => 'application/json'],
                        $responseContent
                    )
                );

            return $mockService;
        });

        $notAllowedRule = Mockery::mock(NotAllowedRule::class);
        $notAllowedRule->shouldReceive('validate')
            ->once()
            ->andThrow(new NotAllowedRuleException());

        app()->instance(NotAllowedRule::class, $notAllowedRule);

        $this->expectException(NotAllowedRuleException::class);

        $authentication = new Authentication();
        $authentication->authenticate('username', 'password');
    }

    /**
     * @throws BindingResolutionException
     * @throws ForbiddenException
     * @throws RequestException
     */
    public function testSystemNotAllowed(): void
    {
        $responseContent = json_encode($this->getResponseSystemNotAllowed());

        app()->bind(RequestService::class, function () use ($responseContent) {
            $mockService = Mockery::mock(RequestService::class);
            $mockService->shouldReceive('__invoke')
                ->once()
                ->andReturn(
                    new Response(
                        200,
                        ['Content-Type' => 'application/json'],
                        $responseContent
                    )
                );

            return $mockService;
        });

        $systemNotAllowedRule = Mockery::mock(SystemNotAllowedRule::class);
        $systemNotAllowedRule->shouldReceive('validate')
            ->once()
            ->andThrow(new SystemNotAllowedRuleException());

        app()->instance(SystemNotAllowedRule::class, $systemNotAllowedRule);

        $this->expectException(SystemNotAllowedRuleException::class);

        $authentication = new Authentication();
        $authentication->authenticate('username', 'password');
    }

    /**
     * @throws BindingResolutionException
     * @throws ForbiddenException
     * @throws RequestException
     */
    public function testReturnArray(): void
    {
        $responseContent = json_encode($this->getResponseADUser());

        app()->bind(RequestService::class, function () use ($responseContent) {
            $mockService = Mockery::mock(RequestService::class);
            $mockService->shouldReceive('__invoke')
                ->once()
                ->andReturn(
                    new Response(
                        200,
                        ['Content-Type' => 'application/json'],
                        $responseContent
                    )
                );

            return $mockService;
        });

        $authentication = new Authentication();

        $this->assertIsArray($authentication->authenticate('username', 'password'));
    }
}
