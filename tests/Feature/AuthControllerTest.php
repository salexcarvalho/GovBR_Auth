<?php

use Salexcarvalho\GovBrAuth\Exceptions\GovBrAuthException;
use Salexcarvalho\GovBrAuth\Services\GovBrOidcService;

// ── redirectToProvider ───────────────────────────────────────────────────────

it('redireciona para a URL de autorização do Gov.br', function () {
    $mock = Mockery::mock(GovBrOidcService::class);
    $mock->shouldReceive('getAuthorizationUrl')
         ->once()
         ->andReturn('https://sso.acesso.gov.br/authorize?client_id=test');

    $this->app->instance(GovBrOidcService::class, $mock);

    $this->get(route('govbr.login'))
         ->assertRedirect('https://sso.acesso.gov.br/authorize?client_id=test');
});

// ── handleProviderCallback – parâmetro error ─────────────────────────────────

it('lança exceção quando Gov.br retorna error no callback', function () {
    $this->withoutExceptionHandling();

    $this->get(route('govbr.callback', ['error' => 'access_denied', 'error_description' => 'Usuário negou o acesso']))
         ->assertStatus(500);
})->throws(GovBrAuthException::class, 'Usuário negou o acesso');

// ── handleProviderCallback – state inválido ───────────────────────────────────

it('lança exceção quando o state é inválido', function () {
    $this->withoutExceptionHandling();

    session()->put('govbr_oauth_state', 'state-correto');

    $this->get(route('govbr.callback', ['code' => 'abc', 'state' => 'state-errado']));
})->throws(GovBrAuthException::class, 'state inválido');

// ── handleProviderCallback – code ausente ────────────────────────────────────

it('lança exceção quando o code está ausente no callback', function () {
    $this->withoutExceptionHandling();

    session()->put('govbr_oauth_state', 'state-correto');

    $this->get(route('govbr.callback', ['state' => 'state-correto']));
})->throws(GovBrAuthException::class, 'Código de autorização não recebido');

// ── logout ───────────────────────────────────────────────────────────────────

it('encerra a sessão Gov.br e redireciona para /', function () {
    session()->put('govbr_token_set', ['access_token' => 'tok']);

    $this->post(route('govbr.logout'))
         ->assertRedirect('/');

    expect(session()->has('govbr_token_set'))->toBeFalse();
});
