<?php
namespace Salexcarvalho\GovBrAuth\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureGovBrAuthenticated
{
    public function handle(Request $req, Closure $next)
    {
        if (! $req->session()->has('govbr_token_set')) {
            return redirect()->route('govbr.login');
        }
        return $next($req);
    }
}
