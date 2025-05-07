<?php
namespace MyOrg\GovBrAuth\Providers;

use Illuminate\Support\ServiceProvider;
use MyOrg\GovBrAuth\Services\GovBrOidcService;

class GovBrAuthServiceProvider extends ServiceProvider
{
    public function register()
    {
        // mescla config
        $this->mergeConfigFrom(__DIR__.'/../../config/govbr.php', 'govbr');

        // registra o service no container
        $this->app->singleton(GovBrOidcService::class, function($app){
            return new GovBrOidcService();
        });
    }

    public function boot()
    {
        // publica config
        $this->publishes([
            __DIR__.'/../../config/govbr.php' => config_path('govbr.php'),
        ], 'govbr-config');

        // carrega rotas
        $this->loadRoutesFrom(__DIR__.'/../../routes/web.php');

        // registra middleware
        $router = $this->app['router'];
        $router->aliasMiddleware('govbr.auth', \MyOrg\GovBrAuth\Middleware\EnsureGovBrAuthenticated::class);
    }
}
