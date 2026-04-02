<?php

use Illuminate\Support\Facades\Route;

beforeEach(function () {
    Route::middleware('govbr.auth')->get('/rota-protegida', fn () => 'ok');
});

it('permite acesso quando a sessão Gov.br está presente', function () {
    session()->put('govbr_token_set', ['access_token' => 'tok']);

    $this->get('/rota-protegida')
         ->assertOk()
         ->assertSee('ok');
});

it('redireciona para govbr.login quando a sessão está ausente', function () {
    $this->get('/rota-protegida')
         ->assertRedirect(route('govbr.login'));
});
