<?php

use Salexcarvalho\GovBrAuth\Exceptions\GovBrAuthException;
use Salexcarvalho\GovBrAuth\Services\GovBrOidcService;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Psr7\Request;

// ── Helpers ─────────────────────────────────────────────────────────────────

function makeService(array $responses = []): GovBrOidcService
{
    $service = app(GovBrOidcService::class);

    if (! empty($responses)) {
        $mock    = new MockHandler($responses);
        $handler = HandlerStack::create($mock);
        $client  = new Client(['handler' => $handler]);

        $reflection = new ReflectionProperty(GovBrOidcService::class, 'http');
        $reflection->setAccessible(true);
        $reflection->setValue($service, $client);
    }

    return $service;
}

// ── getAuthorizationUrl ──────────────────────────────────────────────────────

it('gera uma URL de autorização com os parâmetros corretos', function () {
    $service = makeService();
    $url     = $service->getAuthorizationUrl();

    expect($url)->toContain('https://sso.acesso.gov.br/authorize');
    expect($url)->toContain('response_type=code');
    expect($url)->toContain('client_id=test-client-id');
    expect($url)->toContain('scope=openid');
    expect($url)->toContain('state=');
});

it('armazena o state na sessão ao gerar a URL', function () {
    $service = makeService();
    $service->getAuthorizationUrl();

    expect(session()->has('govbr_oauth_state'))->toBeTrue();
});

it('gera um state diferente a cada chamada', function () {
    $service = makeService();

    $service->getAuthorizationUrl();
    $first = session()->get('govbr_oauth_state');

    $service->getAuthorizationUrl();
    $second = session()->get('govbr_oauth_state');

    expect($first)->not->toBe($second);
});

// ── validateState ────────────────────────────────────────────────────────────

it('valida corretamente um state correto', function () {
    $service = makeService();
    $service->getAuthorizationUrl();
    $state = session()->get('govbr_oauth_state');

    // A validação consome o state da sessão, por isso salvamos antes
    session()->put('govbr_oauth_state', $state);

    expect($service->validateState($state))->toBeTrue();
});

it('rejeita um state incorreto', function () {
    $service = makeService();
    $service->getAuthorizationUrl();

    expect($service->validateState('state-invalido'))->toBeFalse();
});

it('rejeita string vazia como state', function () {
    $service = makeService();
    $service->getAuthorizationUrl();

    expect($service->validateState(''))->toBeFalse();
});

it('consome o state da sessão após validação (uso único)', function () {
    $service = makeService();
    $service->getAuthorizationUrl();
    $state = session()->get('govbr_oauth_state');

    session()->put('govbr_oauth_state', $state);

    $service->validateState($state);

    expect(session()->has('govbr_oauth_state'))->toBeFalse();
});

// ── callback – erros de rede ─────────────────────────────────────────────────

it('lança GovBrAuthException quando o endpoint de token está indisponível', function () {
    $service = makeService([
        new ConnectException('Connection refused', new Request('POST', 'test')),
    ]);

    $service->callback('algum-codigo');
})->throws(GovBrAuthException::class, 'Falha ao comunicar com o endpoint de token do Gov.br');

// ── callback – id_token ausente ───────────────────────────────────────────────

it('lança GovBrAuthException quando id_token está ausente na resposta', function () {
    $service = makeService([
        new Response(200, [], json_encode(['access_token' => 'tok'])),
    ]);

    $service->callback('algum-codigo');
})->throws(GovBrAuthException::class, 'id_token ausente');
