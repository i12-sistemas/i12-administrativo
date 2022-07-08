<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use App\Models\UsuarioTokens;
use App\Models\Usuario;
use App\Models\Clientes;
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
        $service = $request->header('x-auth-service') ? $request->header('x-auth-service') : '';
        $database = $request->header('x-auth-database') ? $request->header('x-auth-database') : '';
        $cnpj = $request->header('x-auth-cnpj') ? $request->header('x-auth-cnpj') : '';
        $accesscode = $request->header('x-auth-accesscode') ? $request->header('x-auth-accesscode') : '';
        $user = $request->header('x-auth-user') ? $request->header('x-auth-user') : '';
        if ($service == ''|| $token == '' || $database == '' || $cnpj == '' || $accesscode == '') throw new Exception('Parametros inválidos');
        
  
        $hashtoken = $service . $database . $cnpj . $accesscode . $user;
        if (!Hash::check($hashtoken, $token)) throw new Exception('Token inválido');

  
        $empresa = Clientes::whereRaw('((cnpj in (' . $cnpj . ')) or (concat("000",cpf) in (' . $cnpj . ')))')->first();
        if (!$empresa) throw new Exception('Empresa não foi encontrada');
        if ($empresa->ativo !== 1) throw new Exception('Empresa inativa');
  
        $usuario = null;
        if ($user ? $user !== '' : false) {
          $usuario = Usuario::where('login', '=', $user)->first();
          if(!$usuario) throw new Exception('Usuário não foi  identificado');
          if($usuario->ativo !== 1) throw new Exception('Usuário inativo');
        }

        session(['empresa' => $empresa]);
        if ($usuario) session(['usuario' => $usuario]);
      } catch (\Throwable $th) {
        return response()->json('Acesso negado: ' . $th->getMessage(), 401);
      }
      return $next($request);
    }
}
