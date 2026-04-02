<?php

namespace Salexcarvalho\GovBrAuth\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Salexcarvalho\GovBrAuth\Providers\GovBrAuthServiceProvider;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [GovBrAuthServiceProvider::class];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('govbr.client_id', 'test-client-id');
        $app['config']->set('govbr.client_secret', 'test-secret');
        $app['config']->set('govbr.redirect_uri', 'https://example.com/auth/govbr/callback');
        $app['config']->set('govbr.authz_endpoint', 'https://sso.acesso.gov.br/authorize');
        $app['config']->set('govbr.token_endpoint', 'https://sso.acesso.gov.br/token');
        $app['config']->set('govbr.jwk_endpoint', 'https://sso.acesso.gov.br/jwk');
        $app['config']->set('govbr.scopes', ['openid', 'profile', 'email']);
        $app['config']->set('govbr.user_model', null);
        $app['config']->set('session.driver', 'array');
    }
}
