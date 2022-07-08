<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        'painelcliente/login/createtoken',
        'painelcliente/login/createtokenrelacionamento',
        'api/v1/empresas/icom/*',
        'api/v1/empresas/licenca/*',
        'api/v1/empresas/contato/*',
        'api/v1/empresas/versao/*',
        'api/v1/empresas/backup/*',
        'api/v1/empresas/contato/resetpwd/external',
        'api/v1/painelcliente/meuschamados/*',
        'api/v2/*'
    ];
}
