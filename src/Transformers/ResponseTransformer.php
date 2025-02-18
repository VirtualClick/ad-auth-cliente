<?php

namespace VirtualClick\AdAuthClient\Transformers;

use VirtualClick\AdAuthClient\Contracts\TransformerInterface;

class ResponseTransformer implements TransformerInterface
{
    /**
     * @inheritDoc
     */
    public static function hanler(array $response): array
    {
        return [
            'usuario' => [
                'cpf' => $response['perfilUsuario']['idUsuario'] ?? null,
                'nome' => $response['perfilUsuario']['nomeUsuario'] ?? null,
            ],
            'ad' => [
                'nome' => $response['usuarioAd']['nome'] ?? null,
                'sobreNome' => $response['usuarioAd']['sobreNome'] ?? null,
                'display_name' => $response['usuarioAd']['displayName'] ?? null,
                'username' => $response['usuarioAd']['siglaUsuarioAd'] ?? null,
                'sigla' => $response['usuarioAd']['sigla'] ?? null,
                'cpf' => $response['usuarioAd']['cpf'] ?? null,
                'email' => $response['usuarioAd']['email'] ?? null,
                'empresa' => $response['usuarioAd']['empresa'] ?? null,
                'departamento' => $response['usuarioAd']['departamento'] ?? null,
                'cargo' => $response['usuarioAd']['cargo'] ?? null,
            ],
            'perfil' => [
                'id' => $response['perfilUsuario']['idPerfil'] ?? null,
                'nome' => $response['perfilUsuario']['nomePerfil'] ?? null,
                'descricao' => $response['perfilUsuario']['descricaoPerfil'] ?? null,
                'tipo' => $response['perfilUsuario']['tipoPerfil'] ?? null,
                'aplicacao' => [
                    'id' => $response['perfilUsuario']['idAplicacao'] ?? null,
                    'nome' => $response['perfilUsuario']['nomeAplicacao'] ?? null,
                    'sigla' => $response['perfilUsuario']['siglaAplicacao'] ?? null,
                ],
            ],
            'status' => [
                'status' => $response['status'],
                'mensagem' => $response['mensagem'],
            ],
        ];
    }
}