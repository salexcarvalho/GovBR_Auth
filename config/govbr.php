<?php

return [
    'client_id'      => env('GOVBR_CLIENT_ID'),
    'client_secret'  => env('GOVBR_CLIENT_SECRET'),
    'redirect_uri'   => env('GOVBR_REDIRECT_URI'),
    'authz_endpoint' => env('GOVBR_AUTHZ_ENDPOINT'),
    'token_endpoint' => env('GOVBR_TOKEN_ENDPOINT'),
    'jwk_endpoint'   => env('GOVBR_JWK_ENDPOINT'),
    'scopes'         => ['openid', 'profile', 'email'],

    /*
    |--------------------------------------------------------------------------
    | Modelo de Usuário
    |--------------------------------------------------------------------------
    | Classe Eloquent usada para criar/atualizar o usuário local após a
    | autenticação. Se não definido, utiliza auth.providers.users.model.
    |
    */
    'user_model'     => env('GOVBR_USER_MODEL', null),
];
