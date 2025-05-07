# GovBrAuth

Pacote Laravel para autenticação via Gov.br usando OIDC (OpenID Connect).

## 📦 Requisitos

* PHP >= 7.4
* Laravel ^8.0 | ^9.0 | ^10.0
* Extensão OpenSSL ativada (para JWT)
* [GuzzleHTTP](https://github.com/guzzle/guzzle)
* [Firebase PHP-JWT](https://github.com/firebase/php-jwt)

## 🚀 Instalação

1. Adicione o repositório (caso esteja usando local via path) no `composer.json` da sua aplicação:

   ```jsonc
   "repositories": [
     {
       "type": "path",
       "url": "vendor/salexcarvalho/govbr-auth"
     }
   ],
   "require": {
     "salexcarvalho/govbr-auth": "*"
   }
   ```

2. Execute:

   ```bash
   composer require salexcarvalho/govbr-auth
   ```

3. Publique as configurações:

   ```bash
   php artisan vendor:publish --tag=govbr-config
   ```

## 🔧 Configuração

Após publicar, edite o arquivo `config/govbr.php` ou defina as variáveis no seu `.env`:

```dotenv
GOVBR_CLIENT_ID=
GOVBR_CLIENT_SECRET=
GOVBR_REDIRECT_URI=https://seu-dominio.com/auth/govbr/callback
GOVBR_AUTHZ_ENDPOINT=https://sso.acesso.gov.br/authorize
GOVBR_TOKEN_ENDPOINT=https://sso.acesso.gov.br/token
GOVBR_JWK_ENDPOINT=https://sso.acesso.gov.br/jwk
```

## 🚧 Rotas

O pacote carrega automaticamente as rotas no prefixo `auth/govbr`. Você terá:

| Método | URI                    | Nome da Rota     | Descrição                           |
| ------ | ---------------------- | ---------------- | ----------------------------------- |
| GET    | `/auth/govbr/redirect` | `govbr.login`    | Redireciona para login Gov.br       |
| GET    | `/auth/govbr/callback` | `govbr.callback` | Callback do Gov.br após autorização |
| POST   | `/auth/govbr/logout`   | `govbr.logout`   | Logout da sessão Gov.br             |

## 🛡️ Protegendo Rotas

Para proteger rotas usando a sessão Gov.br, aplique o middleware:

```php
Route::middleware('govbr.auth')->group(function () {
    Route::get('/dashboard', function () {
        // Acesso apenas para usuários autenticados via Gov.br
    });
});
```

## 🔄 Fluxo de Autenticação

1. Usuário acessa `/auth/govbr/redirect`.
2. É redirecionado ao portal Gov.br para login.
3. Após login, Gov.br retorna para `/auth/govbr/callback` com um `code`.
4. O pacote troca o `code` por `id_token` e `access_token`, valida o JWT via JWK e retorna as claims.
5. Cria/atualiza o usuário local e realiza `Auth::login($user)`.

## 🤝 Contribuição

1. Dê um fork no repositório.
2. Crie uma branch com a feature: `git checkout -b feature/nova-funcionalidade`.
3. Faça commit das suas alterações: `git commit -m 'Adiciona nova funcionalidade'`.
4. Envie para a branch: `git push origin feature/nova-funcionalidade`.
5. Abra um Pull Request.

## 📝 Licença

Este projeto está licenciado sob a licença MIT. Consulte o arquivo [LICENSE](LICENSE) para mais detalhes.
