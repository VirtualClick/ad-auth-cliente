<?php

namespace VirtualClick\AdAuthClient\Services;

use VirtualClick\AdAuthClient\Contracts\AuthenticationInterface;
use VirtualClick\AdAuthClient\Exceptions\AuthenticationException;

class AuthenticationResponseService implements AuthenticationInterface
{
    protected $authService;

    /**
     * @param AuthenticationService $authService
     */
    public function __construct(AuthenticationService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * @param array $credentials
     *
     * @return array
     *
     * @throws AuthenticationException
     */
    public function authenticate(array $credentials): array
    {
        if (!isset($credentials['authKey']) || !isset($credentials['authPass'])) {
            throw new AuthenticationException('Credenciais incompletas');
        }

        $payload = [
            'authKey' => $credentials['authKey'],
            'authPass' => $credentials['authPass'],
            'authKeyType' => config('ad-auth.auth_key_type'),
            'siglaAplicacao' => config('ad-auth.application_code'),
        ];

        $response = $this->authService->authenticate($payload);

        return $this->validate($response);
    }

    /**
     * @param array $response
     *
     * @return array
     *
     * @throws AuthenticationException
     */
    public function validate(array $response): array
    {
        return $this->handleResponse($response);
    }

    /**
     * @param array $response
     *
     * @return array
     *
     * @throws AuthenticationException
     */
    protected function handleResponse(array $response): array
    {
        if (($response['usuarioAtivo'] ?? 'N') !== 'S') {
            throw new AuthenticationException('Usuário inativo');
        }

        if ($response['status'] !== 'OK') {
            if (isset($response['perfilUsuario']['idUsuario'])) {
                throw new AuthenticationException('Usuário sem permissão de acesso ao sistema');
            }

            throw new AuthenticationException($response['mensagem'] ?? 'Usuário ou senha inválidos');
        }

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
