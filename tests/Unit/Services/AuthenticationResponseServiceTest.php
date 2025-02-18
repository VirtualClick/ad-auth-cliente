<?php

namespace VirtualClick\AdAuthClient\Tests\Unit\Services;

use VirtualClick\AdAuthClient\Exceptions\AuthenticationException;
use VirtualClick\AdAuthClient\Services\AuthenticationResponseService;
use VirtualClick\AdAuthClient\Tests\TestCase;

class AuthenticationResponseServiceTest extends TestCase
{
    private $responseService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->responseService = new AuthenticationResponseService();
    }

    public function testHandleResponseWithValidData()
    {
        $response = [
            'usuarioAtivo' => 'S',
            'status' => 'OK',
            'mensagem' => 'Autenticado com sucesso',
            'perfilUsuario' => [
                'idUsuario' => '12345678900',
                'nomeUsuario' => 'John Doe',
                'idPerfil' => '1',
                'nomePerfil' => 'Admin',
                'descricaoPerfil' => 'Administrador',
                'tipoPerfil' => 'ADMIN',
                'idAplicacao' => '123',
                'nomeAplicacao' => 'Test App',
                'siglaAplicacao' => 'TA',
            ],
            'usuarioAd' => [
                'nome' => 'John',
                'sobreNome' => 'Doe',
                'displayName' => 'John Doe',
                'siglaUsuarioAd' => 'jdoe',
                'sigla' => 'JD',
                'cpf' => '12345678900',
                'email' => 'john.doe@example.com',
                'empresa' => 'Example Corp',
                'departamento' => 'IT',
                'cargo' => 'Developer',
            ],
        ];

        $result = $this->responseService->handleResponse($response);

        $this->assertEquals('12345678900', $result['usuario']['cpf']);
        $this->assertEquals('John Doe', $result['usuario']['nome']);
        $this->assertEquals('John', $result['ad']['nome']);
        $this->assertEquals('Developer', $result['ad']['cargo']);
        $this->assertEquals('1', $result['perfil']['id']);
        $this->assertEquals('OK', $result['status']['status']);
    }

    public function testHandleResponseWithInactiveUser()
    {
        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage('Usuário inativo');

        $response = [
            'usuarioAtivo' => 'N',
            'status' => 'OK',
        ];

        $this->responseService->handleResponse($response);
    }

    public function testHandleResponseWithInvalidStatus()
    {
        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage('Usuário ou senha inválidos');

        $response = [
            'usuarioAtivo' => 'S',
            'status' => 'ERROR',
        ];

        $this->responseService->handleResponse($response);
    }

    public function testHandleResponseWithUnauthorizedUser()
    {
        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage('Usuário sem permissão de acesso ao sistema');

        $response = [
            'usuarioAtivo' => 'S',
            'status' => 'ERROR',
            'perfilUsuario' => [
                'idUsuario' => '12345678900',
            ],
        ];

        $this->responseService->handleResponse($response);
    }
}
