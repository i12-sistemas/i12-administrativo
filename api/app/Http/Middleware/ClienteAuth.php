<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ClienteAuth
{
    public function handle($request, Closure $next)
    {   
        if (!Auth::guard('painelcliente')->check()) {
            return redirect()->route('painelcliente.login');
        }

        return $next($request);
    }
}
