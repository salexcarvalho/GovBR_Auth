<?php
return [
    'client_id'      => env('GOVBR_CLIENT_ID'),
    'client_secret'  => env('GOVBR_CLIENT_SECRET'),
    'redirect_uri'   => env('GOVBR_REDIRECT_URI'),
    'authz_endpoint' => env('GOVBR_AUTHZ_ENDPOINT'),
    'token_endpoint' => env('GOVBR_TOKEN_ENDPOINT'),
    'jwk_endpoint'   => env('GOVBR_JWK_ENDPOINT'),
    'scopes'         => ['openid', 'profile', 'email'],
];
