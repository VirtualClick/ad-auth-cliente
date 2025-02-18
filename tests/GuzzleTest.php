<?php

namespace VirtualClick\AdAuthClient\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;

class GuzzleTest extends TestCase
{
    /**
     * @throws GuzzleException
     */
    public function testConnection()
    {
        $client = new Client([
//            'base_uri' => 'https://www33.hermespardini.com.br/ihpsecurityws/autenticarusuario',
            'base_uri' => 'https://hermespardini.com.br/ihpsecurityws/autenticarusuario',
            'timeout' => 30,
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
        ]);

        try {
            $response = $client->post('', [
                'json' => [
                    'authKey' => 'sergio.hess',
                    'authPass' => 'pT6#v8mshQIwnqSjPc$sRmr0iLs$YgZ!',
                    'authKeyType' => 7,
                    'siglaAplicacao' => 'FENIX',
                ],
            ]);

            dd(json_decode($response->getBody()->getContents(), true)['status']);
        } catch (ClientException $e) {
            dd($e->getCode());
        }
    }
}

//[
//    {
//        "mensagem": "Usuário encontrado.",
//        "status": "OK",
//        "usuarioAd": {
//            "nome": "Sergio",
//            "sobreNome": "Posto De Oliveira Hess",
//            "cpf": "31503516857",
//            "empresa": "Toxicologia Pardini",
//            "departamento": "TI ADM",
//            "sigla": "SPOH",
//            "cargo": "DESENVOLVEDOR TI JUNIOR",
//            "email": "sergio.hess@toxicologiapardini.com.br",
//            "displayName": "Sergio Hess",
//            "siglaUsuarioAd": "sergio.hess"
//        },
//        "perfilUsuario": {
//            "id": 142947,
//            "idAplicacao": 48,
//            "idPerfil": 1,
//            "idUsuario": 31503516857,
//            "idEmpresa": 1,
//            "siglaUsuario": "SPOH",
//            "siglaAplicacao": "FENIX",
//            "nomePerfil": "ADMINISTRADOR",
//            "nomeAplicacao": "FENIX",
//            "nomeUsuario": "SERGIO POSTO DE OLIVEIRA HESS",
//            "descricaoPerfil": "ADMINISTRADOR",
//            "tipoPerfil": "A"
//        },
//        "usuarioAtivo": "S",
//        "usuarioIHP": {
//            "id": 31503516857,
//            "sigla": "SPOH",
//            "nome": "Sergio Hess",
//            "chapa": 31503516857,
//            "empresa": "Toxicologia Pardini",
//            "situacao": "A",
//            "usuarioad": "sergio.hess"
//        }
//    },
//    {
//        "mensagem": "Usuário sem permissão de acesso.",
//        "status": "NOK",
//        "perfilUsuario": {
//            "siglaAplicacao": "FENIX"
//        }
//    },
//    {
//        "mensagem": "Usuário sem permissão de acesso.",
//        "status": "NOK",
//        "perfilUsuario": {
//            "idUsuario": 31503516857,
//            "siglaUsuario": "SPOH",
//            "siglaAplicacao": "FENIXXX"
//        },
//        "usuarioAtivo": "S"
//    }
//]
