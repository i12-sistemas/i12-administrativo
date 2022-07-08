<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // if (Auth::check()) {
        //     return redirect()->route('admin.home');
        // }
        
        // if (Auth::guard('admin')->check()) {
        //     return redirect('/admin');
        // }
    

        // //If request comes from logged in seller, he will
        // //be redirected to seller's home page.
        // if (Auth::guard('painelcliente')->check()) {
        //     return redirect('/usuario');
        // }

        return $next($request);
    }
}
