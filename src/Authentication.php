<?php

namespace VirtualClick\AdAuthClient;

use Illuminate\Contracts\Container\BindingResolutionException;
use VirtualClick\AdAuthClient\Contracts\AuthenticationInterface;
use VirtualClick\AdAuthClient\Rules\NotAllowedRule;
use VirtualClick\AdAuthClient\Rules\SystemNotAllowedRule;
use VirtualClick\AdAuthClient\Services\RequestService;
use VirtualClick\AdAuthClient\Transformers\ResponseTransformer;

class Authentication implements AuthenticationInterface
{
    /**
     * @var array
     */
    protected $rules = [
        NotAllowedRule::class,
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

        $responseArray = json_decode($response->getBody()->getContents(), true);
        $this->executeRules($responseArray);

        return ResponseTransformer::hanler($responseArray);
    }

    /**
     * @throws BindingResolutionException
     */
    protected function executeRules(array $response)
    {
        foreach ($this->rules as $rule) {
            app()->make($rule)->validate($response);
        }
    }
}
