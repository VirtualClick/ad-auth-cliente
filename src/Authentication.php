<?php

namespace VirtualClick\AdAuthClient;

use GuzzleHttp\Psr7\Response;
use Illuminate\Contracts\Container\BindingResolutionException;
use VirtualClick\AdAuthClient\Contracts\AuthenticationInterface;
use VirtualClick\AdAuthClient\Rules\NotAllwedRule;
use VirtualClick\AdAuthClient\Rules\SystemNotAllowedRule;
use VirtualClick\AdAuthClient\Services\RequestService;
use VirtualClick\AdAuthClient\Transformers\ResponseTransformer;

class Authentication implements AuthenticationInterface
{
    /**
     * @var array
     */
    protected $rules = [
        NotAllwedRule::class,
        SystemNotAllowedRule::class,
    ];

    /**
     * @param string $username
     * @param string $password
     *
     * @return array|null
     * @throws Exceptions\ForbiddenException
     * @throws Exceptions\RequestException
     * @throws BindingResolutionException
     */
    public function authenticate(
        string $username,
        string $password
    ): ?array {
        $response = app()->make(RequestService::class, [
            'username' => $username,
            'password' => $password,
        ])();

        $this->executeRules($response);

        return ResponseTransformer::hanler($response->getBody()->getContents());
    }

    /**
     * @throws BindingResolutionException
     */
    protected function executeRules(Response $response)
    {
        foreach ($this->rules as $rule) {
            app()->make($rule)->validate($response);
        }
    }
}
