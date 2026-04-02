<?php

namespace Salexcarvalho\GovBrAuth\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Salexcarvalho\GovBrAuth\Exceptions\GovBrAuthException;
use Salexcarvalho\GovBrAuth\Services\GovBrOidcService;

class AuthController extends Controller
{
    public function __construct(protected GovBrOidcService $oidc) {}

    public function redirectToProvider()
    {
        return redirect()->away($this->oidc->getAuthorizationUrl());
    }

    public function handleProviderCallback(Request $req)
    {
        if ($req->has('error')) {
            throw new GovBrAuthException(
                'Erro de autorização Gov.br: ' . $req->get('error_description', $req->get('error'))
            );
        }

        if (! $this->oidc->validateState((string) $req->get('state', ''))) {
            throw new GovBrAuthException('Parâmetro state inválido. Possível ataque CSRF.');
        }

        if (! $req->filled('code')) {
            throw new GovBrAuthException('Código de autorização não recebido.');
        }

        $data   = $this->oidc->callback($req->get('code'));
        $claims = $data['claims'];

        $modelClass = config('govbr.user_model', config('auth.providers.users.model'));

        $user = $modelClass::updateOrCreate(
            ['govbr_sub' => $claims['sub']],
            [
                'name'  => $claims['name'] ?? null,
                'email' => $claims['email'] ?? null,
            ]
        );

        Auth::login($user);
        Session::put('govbr_token_set', $data);

        return redirect()->intended('/');
    }

    public function logout(Request $req)
    {
        Auth::logout();
        $req->session()->forget('govbr_token_set');
        return redirect('/');
    }
}
