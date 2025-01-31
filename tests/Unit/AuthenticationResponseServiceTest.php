<?php

namespace VirtualClick\AdAuthClient\Tests\Unit;

use VirtualClick\AdAuthClient\Exceptions\AuthenticationException;
use VirtualClick\AdAuthClient\Services\AuthenticationResponseService;
use VirtualClick\AdAuthClient\Services\AuthenticationService;
use VirtualClick\AdAuthClient\Tests\TestCase;

class AuthenticationResponseServiceTest extends TestCase
{
    private $service;

    private $authService;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->authService = $this->createMock(AuthenticationService::class);
        $this->service = new AuthenticationResponseService($this->authService);
    }

    /**
     * @return void
     *
     * @throws AuthenticationException
     */
    public function test_throws_exception_for_incomplete_credentials()
    {
        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage('Credenciais incompletas');

        $this->service->authenticate(['authKey' => 'test@test.com']);
    }

    /**
     * @return void
     *
     * @throws AuthenticationException
     */
    public function test_formats_payload_with_config(): void
    {
        $credentials = [
            'authKey' => 'test@test.com',
            'authPass' => 'password',
        ];

        $expectedPayload = [
            'authKey' => 'test@test.com',
            'authPass' => 'password',
            'authKeyType' => config('ad-auth.auth_key_type'),
            'siglaAplicacao' => config('ad-auth.application_code'),
        ];

        $mockApiResponse = [
            'status' => 'OK',
            'mensagem' => 'Usuário encontrado',
            'usuarioAtivo' => 'S',
            'perfilUsuario' => [],
            'usuarioAd' => [],
        ];

        $this->authService->expects($this->once())
            ->method('authenticate')
            ->with($expectedPayload)
            ->willReturn($mockApiResponse);

        $this->service->authenticate($credentials);
    }

    /**
     * @return void
     *
     * @throws AuthenticationException
     */
    public function test_throws_exception_for_inactive_user(): void
    {
        $this->authService->method('authenticate')->willReturn([
            'usuarioAtivo' => 'N',
            'status' => 'OK',
        ]);

        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage('Usuário inativo');

        $this->service->authenticate([
            'authKey' => 'test@test.com',
            'authPass' => 'password',
        ]);
    }

    /**
     * @return void
     *
     * @throws AuthenticationException
     */
    public function test_throws_exception_for_user_without_permission(): void
    {
        $this->authService->method('authenticate')
            ->willReturn([
                'status' => 'NOK',
                'mensagem' => 'Usuário sem permissão de acesso.',
                'usuarioAtivo' => 'S',
                'perfilUsuario' => [
                    'idUsuario' => '12345678900',
                ],
            ]);

        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage('Usuário sem permissão de acesso ao sistema');

        $this->service->authenticate([
            'authKey' => 'test@test.com',
            'authPass' => 'password',
        ]);
    }

    /**
     * @return void
     *
     * @throws AuthenticationException
     */
    public function test_throws_exception_for_invalid_credentials(): void
    {
        $this->authService->method('authenticate')->willReturn([
            'status' => 'NOK',
            'mensagem' => 'Usuário ou senha inválidos',
            'usuarioAtivo' => 'S',
            'perfilUsuario' => [],
        ]);

        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage('Usuário ou senha inválidos');

        $this->service->authenticate([
            'authKey' => 'test@test.com',
            'authPass' => 'password',
        ]);
    }

    /**
     * @return void
     *
     * @throws AuthenticationException
     */
    public function test_returns_formatted_successful_response(): void
    {
        $apiResponse = [
            'status' => 'OK',
            'mensagem' => 'Usuário encontrado.',
            'usuarioAtivo' => 'S',
            'usuarioAd' => [
                'nome' => 'João',
                'sobreNome' => 'Silva',
                'cpf' => '12345678900',
                'empresa' => 'VirtualClick',
                'departamento' => 'TI',
                'sigla' => 'JS',
                'cargo' => 'Desenvolvedor',
                'email' => 'joao.silva@virtualclick.com.br',
                'displayName' => 'João Silva',
                'siglaUsuarioAd' => 'joao.silva',
            ],
            'perfilUsuario' => [
                'id' => 123,
                'idAplicacao' => 49,
                'idPerfil' => 1,
                'idUsuario' => '12345678900',
                'nomeUsuario' => 'João Silva',
                'nomePerfil' => 'ADMINISTRADOR',
                'descricaoPerfil' => 'ADMINISTRADOR',
                'tipoPerfil' => 'A',
                'nomeAplicacao' => 'Sistema',
                'siglaAplicacao' => 'TEST',
            ],
        ];

        $this->authService->method('authenticate')->willReturn($apiResponse);

        $result = $this->service->authenticate([
            'authKey' => 'test@test.com',
            'authPass' => 'password',
        ]);

        $this->assertEquals('12345678900', $result['usuario']['cpf']);
        $this->assertEquals('João Silva', $result['usuario']['nome']);
        $this->assertEquals('João', $result['ad']['nome']);
        $this->assertEquals('Silva', $result['ad']['sobreNome']);
        $this->assertEquals('joao.silva', $result['ad']['username']);
        $this->assertEquals('ADMINISTRADOR', $result['perfil']['nome']);
        $this->assertEquals('OK', $result['status']['status']);
    }
}
