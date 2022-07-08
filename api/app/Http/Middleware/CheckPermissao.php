<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckPermissao
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $idpermissao)
    {
        if (!Auth::guard('admin')->User()->permite($idpermissao)) {
            return redirect()->route('acessonegado', [$idpermissao]);
        }

        return $next($request);
    }
}
