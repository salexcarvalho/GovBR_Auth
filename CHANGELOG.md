# Changelog

Todas as mudanças notáveis neste projeto serão documentadas aqui.

O formato segue o [Keep a Changelog](https://keepachangelog.com/pt-BR/1.0.0/)
e este projeto adere ao [Versionamento Semântico](https://semver.org/lang/pt-BR/).

---

## [Unreleased]

### Adicionado
- Suporte ao Laravel 12.x
- Testes automatizados com Pest
- GitHub Actions CI com matriz PHP 8.1–8.3 × Laravel 10–12
- Templates de issue e PR
- `CONTRIBUTING.md` e `CHANGELOG.md`

### Corrigido
- Validação real do parâmetro `state` via sessão com `hash_equals()` (proteção CSRF)
- Tratamento de erros no callback: `error` do Gov.br, falhas de rede e JWT inválido
- Remoção de dependência hard-coded em `App\Models\User` e `App\Http\Controllers\Controller`

### Alterado
- `AuthController` agora usa `Illuminate\Routing\Controller` (sem dependência do app)
- Modelo de usuário configurável via `govbr.user_model` ou `auth.providers.users.model`

---

## [1.0.0] — 2024-01-01

### Adicionado
- Autenticação OIDC com Gov.br (fluxo authorization code)
- Validação de JWT via JWK com cache de 24 horas
- Middleware `govbr.auth` para proteção de rotas
- Suporte a Laravel 8.x, 9.x, 10.x e 11.x
