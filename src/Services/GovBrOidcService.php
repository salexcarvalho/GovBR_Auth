<?php
namespace Salexcarvalho\GovBrAuth\Services;

use GuzzleHttp\Client;
use Firebase\JWT\JWT;
use Firebase\JWT\JWK;
use Illuminate\Support\Facades\Cache;

class GovBrOidcService
{
    protected $cfg;
    protected $http;

    public function __construct()
    {
        $this->cfg  = config('govbr');
        $this->http = new Client();
    }

    public function getAuthorizationUrl(): string
    {
        $qs = http_build_query([
            'response_type' => 'code',
            'client_id'     => $this->cfg['client_id'],
            'redirect_uri'  => $this->cfg['redirect_uri'],
            'scope'         => implode(' ', $this->cfg['scopes']),
            'state'         => csrf_token(),
        ]);

        return "{$this->cfg['authz_endpoint']}?{$qs}";
    }

    public function callback(string $code): array
    {
        // troca código por token
        $resp = $this->http->post($this->cfg['token_endpoint'], [
            'form_params' => [
                'grant_type'    => 'authorization_code',
                'code'          => $code,
                'redirect_uri'  => $this->cfg['redirect_uri'],
                'client_id'     => $this->cfg['client_id'],
                'client_secret' => $this->cfg['client_secret'],
            ],
        ]);
        $set  = json_decode($resp->getBody(), true);

        // JWKs (cache 24h)
        $jwks = Cache::remember('govbr_jwks', 1440, function(){
            $r = $this->http->get($this->cfg['jwk_endpoint']);
            return json_decode($r->getBody(), true);
        });

        // valida id_token
        $claims = JWT::decode($set['id_token'], JWK::parseKeySet($jwks), ['RS256']);

        return array_merge($set, ['claims' => (array)$claims]);
    }
}
