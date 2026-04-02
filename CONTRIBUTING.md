# Como Contribuir

Obrigado pelo interesse em contribuir com o **GovBR Auth**!

## Configuração do ambiente

```bash
git clone https://github.com/salexcarvalho/GovBR_Auth.git
cd GovBR_Auth
composer install
```

## Rodando os testes

```bash
composer test
```

## Padrões de código

- Siga o [PSR-12](https://www.php-fig.org/psr/psr-12/) para estilo de código
- Escreva testes para toda funcionalidade nova ou corrigida
- Mantenha retrocompatibilidade com PHP 7.4+

## Processo de contribuição

1. Abra uma [issue](https://github.com/salexcarvalho/GovBR_Auth/issues) descrevendo o que pretende fazer
2. Faça um fork e crie sua branch a partir de `main`: `git checkout -b feature/nome-da-feature`
3. Implemente a mudança com testes
4. Garanta que todos os testes passam: `composer test`
5. Atualize o `CHANGELOG.md` na seção `[Unreleased]`
6. Abra o Pull Request preenchendo o template

## Convenção de commits

Use o padrão [Conventional Commits](https://www.conventionalcommits.org/):

```
feat: adiciona suporte a PKCE
fix: corrige validação do state em sessões stateless
docs: atualiza exemplo de instalação
test: adiciona testes para middleware
```

## Reportando vulnerabilidades de segurança

**Não abra issues públicas para vulnerabilidades de segurança.**  
Entre em contato diretamente via e-mail antes de qualquer divulgação pública.
