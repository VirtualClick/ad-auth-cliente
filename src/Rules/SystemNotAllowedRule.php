<?php

namespace VirtualClick\AdAuthClient\Rules;

use GuzzleHttp\Psr7\Response;
use VirtualClick\AdAuthClient\Contracts\RuleInterface;
use VirtualClick\AdAuthClient\Exceptions\SystemNotAllowedRuleException;

class SystemNotAllowedRule implements RuleInterface
{
    /**
     * @inheritDoc
     */
    public function validate(Response $response): void
    {
        $responseArray = json_decode($response->getBody()->getContents(), true);

        if ($responseArray['status'] === 'NOK') {
            throw new SystemNotAllowedRuleException(
                'IHP Security: Usuário sem permissão de acesso ao ' . $responseArray['perfilUsuario']['siglaAplicacao'] . '. Favor verificar com o suporte da Toxicologia.'
            );
        }
    }
}
