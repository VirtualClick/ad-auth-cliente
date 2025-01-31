# Cliente de Autenticação AD

Cliente Laravel para autenticação via API do Active Directory.

## Requisitos

- PHP ^7.2|^8.0
- Laravel ^5.1|^6.0|^7.0|^8.0|^9.0|^10.0|^11.0

## Instalação

1. Adicione o repositório no seu `composer.json`:
```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "git@github.com:VirtualClick/ad-auth-cliente.git"
        }
    ]
}
```

2. Instale via Composer:
```bash
composer require virtualclick/ad-auth-client
```

3. Publique o arquivo de configuração:
```bash
php artisan vendor:publish --provider="VirtualClick\AdAuthClient\AdAuthServiceProvider"
```

4. Configure as variáveis de ambiente no seu `.env`:
```env
AD_AUTH_BASE_URL=https://sua-api.com/auth
AD_AUTH_TIMEOUT=30
AD_AUTH_APPLICATION_CODE=SIGLA
AD_AUTH_KEY_TYPE=0
```

## Uso

### Usando Injeção de Dependência

```php
use VirtualClick\AdAuthClient\Contracts\AuthenticationInterface;

class LoginController
{
    public function login(AuthenticationInterface $auth)
    {
        try {
            $userData = $auth->authenticate([
                'authKey' => $request->username,
                'authPass' => $request->password
            ]);
            
            // $userData contém:
            // $userData['usuario'] - Dados do usuário
            // $userData['ad'] - Dados do AD
            // $userData['perfil'] - Dados do perfil
            
        } catch (AuthenticationException $e) {
            // Trata os possíveis erros:
            // - "Usuário inativo"
            // - "Usuário sem permissão de acesso ao sistema"
            // - "Usuário ou senha inválidos"
        }
    }
}
```

### Usando Facade

```php
use VirtualClick\AdAuthClient\Facades\AdAuth;

$userData = AdAuth::authenticate([
    'authKey' => $request->username,
    'authPass' => $request->password
]);
```

## Estrutura da Resposta

Em caso de sucesso, o método `authenticate()` retorna um array com a seguinte estrutura:

```php
[
    'usuario' => [
        'cpf' => string,
        'nome' => string,
    ],
    'ad' => [
        'nome' => string,
        'sobreNome' => string,
        'display_name' => string,
        'username' => string,
        'sigla' => string,
        'cpf' => string,
        'email' => string,
        'empresa' => string,
        'departamento' => string,
        'cargo' => string,
    ],
    'perfil' => [
        'id' => int,
        'nome' => string,
        'descricao' => string,
        'tipo' => string,
        'aplicacao' => [
            'id' => int,
            'nome' => string,
            'sigla' => string,
        ],
    ],
    'status' => [
        'status' => string,
        'mensagem' => string,
    ],
]
```

## Desenvolvimento

### Setup com Docker

1. Clone o repositório:
```bash
git clone git@github.com:virtualclick/ad-auth-client.git
cd ad-auth-client
```

2. Inicie o container:
```bash
docker-compose up -d
```

3. Instale as dependências:
```bash
docker-compose exec app composer install
```

### Rodando os Testes

```bash
docker-compose exec app composer test
```

Para gerar relatório de cobertura:
```bash
docker-compose exec app composer test-coverage
```

## Licença

Este pacote é um software proprietário da VirtualClick.
