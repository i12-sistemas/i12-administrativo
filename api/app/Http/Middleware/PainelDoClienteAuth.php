<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use App\models\ContatoTokens;
use App\models\Contato;
use App\models\ContatoCliente;
use App\models\Clientes;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class PainelDoClienteAuth
{
  public function handle($request, Closure $next)
  {
    try {
      $token = $request->header('x-auth-token') ? $request->header('x-auth-token') : '';
      $accesscode = $request->header('x-auth-accesscode') ? $request->header('x-auth-accesscode') : '';
      $username = ($request->header('x-auth-username') ? $request->header('x-auth-username') : '');
      if ($username == ''|| $token=='') throw new Exception('Parametros inválidos');


      $usuariosessao = ContatoTokens::where('token', '=', $token)
                                    ->where('accesscode', '=', $accesscode)
                                    ->where('username', '=', $username)
                                    ->first();
      if(!$usuariosessao) throw new Exception('Sessão inválida');
      if($usuariosessao->expire_at < Carbon::Now()) throw new Exception('Sessão expirada');

      if(!$usuariosessao->contato) throw new Exception('Sessão incompleta - sem contato asssociado');
      if(!$usuariosessao->cliente) throw new Exception('Sessão incompleta - sem empresa asssociado');

      $contato = $usuariosessao->contato;
      $cliente = $usuariosessao->cliente;

      if($cliente->ativo !== 1) throw new Exception('Sessão inválida - Cliente inativo');

      $permissoes = ContatoCliente::where('idcontato', $contato->id)->where('idcliente', $cliente->codcliente)->first();
      if(!$permissoes) throw new Exception('Nenhum permissão encontrada no perfil usuario <> cliente');

      session(['permissoes' => $permissoes]);
      session(['contato' => $contato]);
      session(['cliente' => $cliente]);
    } catch (\Throwable $th) {
      return response()->json('Acesso negado: ' . $th->getMessage(), 401);
    }
    return $next($request);
  }
}
