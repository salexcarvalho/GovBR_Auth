<?php
namespace Salexcarvalho\GovBrAuth\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Controller;
use Salexcarvalho\GovBrAuth\Services\GovBrOidcService;
use App\Models\User;

class AuthController extends Controller
{
    public function __construct(protected GovBrOidcService $oidc) {}

    public function redirectToProvider()
    {
        return redirect()->away($this->oidc->getAuthorizationUrl());
    }

    public function handleProviderCallback(Request $req)
    {
        $data   = $this->oidc->callback($req->get('code'));
        $claims = $data['claims'];

        // Exemplo: mapeia ou cria usuário
        $user = User::updateOrCreate(
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
