<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use App\Models\UsuarioTokens;
use App\Models\Usuario;
use App\Models\Empresa;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class AuthUserAdmin
{
  public function handle($request, Closure $next)
  {

    try {
      $token = $request->header('x-auth-token') ? $request->header('x-auth-token') : '';
      $username = ($request->header('x-auth-username') ? $request->header('x-auth-username') : '');
      if ($username == ''|| $token=='') throw new Exception('Parametros inválidos');


      $empresa = Empresa::where('cnpj', '=', ENV('CNPJ_I12'))->first();
      if (!$empresa) throw new Exception('Empresa não foi encontrada');
      if ($empresa->cnpj !== ENV('CNPJ_I12')) throw new Exception('Subdomino não confere com o CNPJ');
      if ($empresa->ativo !== 1) throw new Exception('Empresa inativa');
      if ($empresa->ativomovimento !== 1) throw new Exception('Empresa inativa operacionalmente');

      $usuariosessao = UsuarioTokens::where('token', '=', $token)->where('idempresa', '=', $empresa->codempresa)->first();
      if(!$usuariosessao) throw new Exception('Sessão inválida');
      if($usuariosessao->expire_at < Carbon::Now()) throw new Exception('Sessão expirada');

      if(!$usuariosessao->empresa) throw new Exception('Sessão incompleta - sem empresa identificada');
      if($usuariosessao->empresa->cnpj !== ENV('CNPJ_I12')) throw new Exception('Sessão inválida para empresa informada');
      if($usuariosessao->empresa->ativo !== 1) throw new Exception('Sessão inválida - Empresa inativa');
      if($usuariosessao->empresa->ativomovimento !== 1) throw new Exception('Sessão inválida - Empresa não operacional');

      $usuario = $usuariosessao->usuario;
      if(!$usuario) throw new Exception('Sessão incompleta - sem usuário identificado');
      if(mb_strtolower($usuario->login) !== mb_strtolower($username)) throw new Exception('Sessão inválida para o usuário informado');
      if($usuario->ativo !== 1) throw new Exception('Sessão inválida - Usuário inativo');

      $hashtoken = $usuario->login . $usuario->senha . $usuario->codusuario . $usuariosessao->empresa->codempresa;
      if (!Hash::check($hashtoken, $token)) throw new Exception('Hash token empresa inválido');

      session(['usuario' => $usuario]);
      session(['empresa' => $empresa]);
    } catch (\Throwable $th) {
      return response()->json('Acesso negado: ' . $th->getMessage(), 401);
    }
    return $next($request);
  }
}
