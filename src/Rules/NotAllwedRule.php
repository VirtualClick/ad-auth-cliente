<?php

namespace VirtualClick\AdAuthClient\Rules;

use GuzzleHttp\Psr7\Response;
use VirtualClick\AdAuthClient\Contracts\RuleInterface;
use VirtualClick\AdAuthClient\Exceptions\NotAllowedRuleException;

class NotAllwedRule implements RuleInterface
{
    /**
     * @inheritDoc
     */
    public function validate(Response $response): void
    {
        $responseArray = json_decode($response->getBody()->getContents(), true);

        if (! isset($responseArray['usuarioAtivo']) || $responseArray['usuarioAtivo'] === 'N') {
            throw new NotAllowedRuleException(
                'IHP Security: Erro login/senha incorretos para o ' . $responseArray['perfilUsuario']['siglaAplicacao'] . '.'
            );
        }
    }
}
