<?php

namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;

class CorsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $headers =  [
            // '*',
            'access-control-allow-headers',
            'access-control-allow-methods',
            'access-control-allow-origin',
            'authorization',
            'Access-Control-Request-Headers',
            'Access-Control-Allow-Methods',
            'Access-Control-Allow-Origin',
            'Authorization',
            'Content-Type',
            'content-type',
            'Accept',
            'x-auth-token',
            'x-auth-username',
            'x-auth-accesscode',
            'x-auth-service',
            'x-auth-cnpj',
            'x-auth-timestamp',
            'accesscode',
            'uuid',
            'token',
            'cnpj',
            'username',
        ];




        $secure_connection = false;
        $protocol = $secure_connection ? 'https://' : 'http://';
        if(isset($_SERVER['HTTPS'])) {
            if ($_SERVER['HTTPS'] == "on") {
                $secure_connection = true;
            }
        }
        // $http_origin = $protocol . $request->server('HTTP_ORIGIN');
        $http_origin = $request->server('HTTP_ORIGIN');
        if ($http_origin ? $http_origin === '' : true) $http_origin = $request->server('HTTP_HOST');
        $temhttp = strpos($http_origin, 'http');
        if ($temhttp === false) $http_origin = $protocol . $http_origin;
        $origin_allowed =  [
            '*',
            'http://i12sistemas.icom.api.local:9001',
            'https://localhost:8080/',
            'http://localhost:8089',
            'http://localhost:8089/',
            'https://localhost:8089',
            'http://localhost:8080/',
            'http://192.168.0.112:8089/',
            'http://192.168.0.112:8089',
            'http://192.168.0.112',
            'http://localhost',
        ];
        $origin = '*';
        if (in_array($http_origin , $origin_allowed)) $origin = $http_origin;
        if (in_array('*' , $origin_allowed)) $origin = '*';
        $origin = '*';

        $response = $next($request);
        $response->headers->set('Access-Control-Allow-Origin', $origin);
        $response->headers->set('Access-Control-Allow-Methods', "GET, POST, OPTIONS, PUT, PATCH, DELETE");
        $response->headers->set('Access-Control-Allow-Headers', implode(',', $headers));
        return $response;
    }
}
