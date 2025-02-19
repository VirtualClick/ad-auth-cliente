<?php

namespace VirtualClick\AdAuthClient\Rules;

use GuzzleHttp\Psr7\Response;
use VirtualClick\AdAuthClient\Contracts\RuleInterface;
use VirtualClick\AdAuthClient\Exceptions\NotAllowedRuleException;

class NotAllowedRule implements RuleInterface
{
    /**
     * @inheritDoc
     *
     * @throws NotAllowedRuleException
     */
    public function validate(array $response): void
    {
        if (! isset($response['usuarioAtivo']) || $response['usuarioAtivo'] === 'N') {
            throw new NotAllowedRuleException(
                'IHP Security: Erro login/senha incorretos para o ' . $response['perfilUsuario']['siglaAplicacao'] . '.'
            );
        }
    }
}
