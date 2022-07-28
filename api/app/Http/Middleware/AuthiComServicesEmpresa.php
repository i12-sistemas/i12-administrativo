<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use App\models\Contato;
use App\models\Clientes;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
class AuthiComServicesEmpresa
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
      try {
        $token = $request->bearerToken();
        $service = $request->hasHeader('x-auth-service') ? $request->header('x-auth-service') : '';
        $database = $request->hasHeader('x-auth-database') ? $request->header('x-auth-database') : '';
        $cnpj = $request->hasHeader('x-auth-cnpj') ? $request->header('x-auth-cnpj') : '';
        $accesscode = $request->hasHeader('x-auth-accesscode') ? $request->header('x-auth-accesscode') : '';
        $user = $request->hasHeader('x-auth-user') ? $request->header('x-auth-user') : '';
        if ($service == ''|| $token == '' || $database == '' || $cnpj == '' || $accesscode == '') throw new Exception('Parametros inválidos');
        
  
        $hashtoken = $service . $database . $cnpj . $accesscode . $user;
        if (!Hash::check($hashtoken, $token)) throw new Exception('Token inválido [1]');

  
        $empresa = Clientes::whereRaw('((cnpj in (' . $cnpj . ')) or (concat("000",cpf) in (' . $cnpj . ')))')->first();
        if (!$empresa) throw new Exception('Empresa não foi encontrada');
        if ($empresa->ativo !== 1) throw new Exception('Empresa inativa');
  
        $contato = null;
        if ($user ? $user !== '' : false) {
          $field = isemail($user) ? 'emailprincipal' : 'whatsapp';
          $contato = Contato::where($field, '=', $user)->first();
          if (!$contato) throw new Exception('Nenhum usuário encontrado');
        }

        
        session(['empresa' => $empresa]);
        session(['cliente' => $empresa]);
        if ($contato) session(['contato' => $contato]);
      } catch (\Throwable $th) {
        return response()->json('Acesso negado: ' . $th->getMessage(), 401);
      }
      return $next($request);
    }
}
