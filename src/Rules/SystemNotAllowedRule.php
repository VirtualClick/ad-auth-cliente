<?php

namespace VirtualClick\AdAuthClient\Rules;

use VirtualClick\AdAuthClient\Contracts\RuleInterface;
use VirtualClick\AdAuthClient\Exceptions\SystemNotAllowedRuleException;

class SystemNotAllowedRule implements RuleInterface
{
    /**
     * @inheritDoc
     *
     * @throws SystemNotAllowedRuleException
     */
    public function validate(array $response): void
    {
        if ($response['status'] === 'NOK') {
            throw new SystemNotAllowedRuleException(
                'IHP Security: Usuário sem permissão de acesso ao ' . $response['perfilUsuario']['siglaAplicacao'] . '. Favor verificar com o suporte da Toxicologia.'
            );
        }
    }
}
