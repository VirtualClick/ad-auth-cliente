<?php

namespace VirtualClick\AdAuthClient\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use VirtualClick\AdAuthClient\AdAuthServiceProvider;

abstract class TestCase extends Orchestra
{
    /**
     * @param $app
     *
     * @return array
     */
    protected function getPackageProviders($app): array
    {
        return [
            AdAuthServiceProvider::class,
        ];
    }

    /**
     * @return string
     */
    protected function getResponseNotAllowed(): string
    {
        return json_encode([
            'mensagem' => 'Usuário sem permissão de acesso.',
            'status' => 'NOK',
            'perfilUsuario' => [
                'siglaAplicacao' => 'AAAAAAAAA',
            ],
        ]);
    }

    /**
     * @return string
     */
    protected function getResponseSystemNotAllowed(): string
    {
        return json_encode([
            'mensagem' => 'Usuário sem permissão de acesso.',
            'status' => 'NOK',
            'perfilUsuario' => [
                'idUsuario' => 12345678909,
                'siglaUsuario' => 'AAAA',
                'siglaAplicacao' => 'AAAAAAAAA',
            ],
            'usuarioAtivo' => 'S',
        ]);
    }

    /**
     * @return string
     */
    protected function getResponseADUser(): string
    {
        return json_encode([
            'mensagem' => 'Usuário encontrado.',
            'status' => 'OK',
            'usuarioAd' => [
                'nome' => 'Aaaaaa',
                'sobreNome' => 'Aaaaa Aaa Aaaaaaa',
                'cpf' => '12345678909',
                'empresa' => 'Aaaaaaaaa Aaaaa',
                'departamento' => 'AA AAAA',
                'sigla' => 'AAAA',
                'cargo' => 'AAAAAAAA AA AAAAAAAA',
                'email' => 'aaaaaa.aaaaaaa@aaaaaaaa.com',
                'displayName' => 'Aaaaaa Aaaaaaa',
                'siglaUsuarioAd' => 'aaaaaa.aaaaaaa',
            ],
            'perfilUsuario' => [
                'id' => 142947,
                'idAplicacao' => 48,
                'idPerfil' => 1,
                'idUsuario' => 12345678909,
                'idEmpresa' => 1,
                'siglaUsuario' => 'AAAA',
                'siglaAplicacao' => 'AAAAAAAAA',
                'nomePerfil' => 'AAAAAAAA',
                'nomeAplicacao' => 'AAAAAAAAA',
                'nomeUsuario' => 'AAAAAA AAAAA AAA AAAAAAA',
                'descricaoPerfil' => 'AAAAAAAA',
                'tipoPerfil' => 'A',
            ],
            'usuarioAtivo' => 'S',
            'usuarioIHP' => [
                'id' => 12345678909,
                'sigla' => 'AAAA',
                'nome' => 'Aaaaaa Aaaaaaa',
                'chapa' => 12345678909,
                'empresa' => 'Aaaaaaaaa Aaaaa',
                'situacao' => 'A',
                'usuarioad' => 'aaaaaa.aaaaaaa',
            ],
        ]);
    }
}
