<?php

namespace Salexcarvalho\GovBrAuth\Services;

use Firebase\JWT\JWK;
use Firebase\JWT\JWT;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Salexcarvalho\GovBrAuth\Exceptions\GovBrAuthException;

class GovBrOidcService
{
    protected array $cfg;
    protected Client $http;

    public function __construct()
    {
        $this->cfg  = config('govbr');
        $this->http = new Client();
    }

    public function getAuthorizationUrl(): string
    {
        $state = Str::random(40);
        Session::put('govbr_oauth_state', $state);

        $qs = http_build_query([
            'response_type' => 'code',
            'client_id'     => $this->cfg['client_id'],
            'redirect_uri'  => $this->cfg['redirect_uri'],
            'scope'         => implode(' ', $this->cfg['scopes']),
            'state'         => $state,
        ]);

        return "{$this->cfg['authz_endpoint']}?{$qs}";
    }

    public function validateState(string $state): bool
    {
        $stored = Session::pull('govbr_oauth_state');

        return $stored !== null && hash_equals($stored, $state);
    }

    public function callback(string $code): array
    {
        try {
            $resp = $this->http->post($this->cfg['token_endpoint'], [
                'form_params' => [
                    'grant_type'    => 'authorization_code',
                    'code'          => $code,
                    'redirect_uri'  => $this->cfg['redirect_uri'],
                    'client_id'     => $this->cfg['client_id'],
                    'client_secret' => $this->cfg['client_secret'],
                ],
            ]);
        } catch (GuzzleException $e) {
            throw new GovBrAuthException('Falha ao comunicar com o endpoint de token do Gov.br: ' . $e->getMessage(), 0, $e);
        }

        $set = json_decode($resp->getBody(), true);

        if (empty($set['id_token'])) {
            throw new GovBrAuthException('Resposta do token inválida: id_token ausente.');
        }

        try {
            $jwks = Cache::remember('govbr_jwks', 1440, function () {
                $r = $this->http->get($this->cfg['jwk_endpoint']);
                return json_decode($r->getBody(), true);
            });
        } catch (GuzzleException $e) {
            throw new GovBrAuthException('Falha ao obter as chaves JWK do Gov.br: ' . $e->getMessage(), 0, $e);
        }

        try {
            $claims = JWT::decode($set['id_token'], JWK::parseKeySet($jwks));
        } catch (\Throwable $e) {
            throw new GovBrAuthException('Falha na validação do id_token: ' . $e->getMessage(), 0, $e);
        }

        return array_merge($set, ['claims' => (array) $claims]);
    }
}
