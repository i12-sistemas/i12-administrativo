<?php

namespace App\Http\Controllers\api\v1\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Exception;
use Carbon\Carbon;
use App\Http\Controllers\RetApiController;

use Validator;
use App\models\Usuario;
use App\models\Empresa;
use App\models\UsuarioTokens;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;


class UserAuthController extends Controller
{
  // autenticação
  public function auth(Request $request)
  {
    $ret = new RetApiController;
    // autenticação de usuário e senha chega no header
    try {
      header('Cache-Control: no-cache, must-revalidate, max-age=0');

      $has_supplied_credentials = !(empty($_SERVER['PHP_AUTH_USER']) && empty($_SERVER['PHP_AUTH_PW']));
      if (!$has_supplied_credentials) 
        throw new Exception('Acesso não autorizado!');

      $forcenewsession = isset($request->forcenewsession) ? IntVal($request->forcenewsession) : 0;
      $forcenewsession = ($forcenewsession == 1) || ($forcenewsession == '1');
      $AUTH_USER = $_SERVER['PHP_AUTH_USER'];
      $AUTH_PASS = $_SERVER['PHP_AUTH_PW'];
      $AUTH_PASS = mysqlPassword($AUTH_PASS);

      $usuario = Usuario::where('login', $AUTH_USER)
                          ->where('senha', $AUTH_PASS)
                          ->where('ativo', 1)
                          ->first();
      if (!$usuario) 
        throw new Exception('Acesso não autorizado!');

      $empresa = $usuario->empresa(ENV('CNPJ_I12'));
      if (!$empresa) throw new Exception('Usuário sem permissão a está empresa');
      if (!($empresa->codempresa > 0)) throw new Exception('Usuário sem permissão a está empresa');

    } catch (\Throwable $th) {
      $ret->msg = $th->getMessage();
      return $ret->toJson();
    }


    try {
      session_start();

      // if ($forcenewsession) 
      session_regenerate_id();
      $sessionid = session_id();

      DB::connection('mysql')->beginTransaction();

      $newsession = UsuarioTokens::find($sessionid);
      if (!$newsession) {
        $newsession = new UsuarioTokens;
        $newsession->session = session_id();
        $newsession->idusuario = $usuario->codusuario;
        $newsession->username = $usuario->login;
        $newsession->idempresa = $empresa->codempresa;
        // $session->ip = \Request::ip();
        $newsession->expire_at = Carbon::now()->addMonth(3);
        $newsession->token = \Hash::make($usuario->login . $usuario->senha . $usuario->codusuario . $empresa->codempresa, ['rounds' => 12]);
        $newsession->ip = \Request::ip();
        $newsession->expire_at = Carbon::now()->addMonth(3);
        $newsession->save();
      }

      DB::connection('mysql')->commit();
      $ret->data = [  
                    'token' => $newsession->token,
                    'expire_at' => $newsession->expire_at->format('Y-m-d H:i:s'),
                    'login'     =>  $usuario->login,
                  ];
      $ret->ok = true;

    } catch (\Throwable $th) {
      DB::connection('mysql')->rollBack();
      $ret->msg = $th->getMessage();
    }
    return $ret->toJson();
  }


  // controller de autenticação da api
  // primeira etapa de auth
  public function checklogin(Request $request)
  {
    $ret = new RetApiController;
    // autenticação de usuário
    try {
      header('Cache-Control: no-cache, must-revalidate, max-age=0');

      $has_supplied_credentials = !(empty($_SERVER['PHP_AUTH_USER']) && empty($_SERVER['PHP_AUTH_PW']));
      if (!$has_supplied_credentials) 
        throw new Exception('Acesso não autorizado!');

      $loginuser = trim($_SERVER['PHP_AUTH_USER']);
      if ($loginuser == '') throw new Exception('Usuario inválido');

      $tokenuser = $_SERVER['PHP_AUTH_PW'];
      if ($tokenuser == '') throw new Exception('Token inválido');


      $empresa = Empresa::where('cnpj', '=', ENV('CNPJ_I12'))->first();
      if (!$empresa) throw new Exception('Empresa não foi encontrada');
      if ($empresa->ativo !== 1) throw new Exception('Empresa inativa');
      if ($empresa->ativomovimento !== 1) throw new Exception('Empresa inativa operacionalmente');

      $session = UsuarioTokens::where('token', $tokenuser)->where('idempresa', '=', $empresa->codempresa)->first();
      if (!$session) throw new Exception('Sessão não foi encontrada');
      if ($session->expire_at < Carbon::now()) throw new Exception('Sessão expirada. Refaça seu login.');
      if (!$session->idempresa) throw new Exception('Sessão incompleta. Refaça seu login.');

      $usuario = $session->usuario;
      $htoken = $usuario->login . $usuario->senha . $usuario->codusuario . $empresa->codempresa;
      if (!Hash::check($htoken, $tokenuser)) throw new Exception('Hash token inválido para a empresa');

      if ($usuario->ativo != 1) throw new Exception('Usuário inativo');

      $dadosusuario = $usuario->export(True);
      $dadosusuario['permissoes'] = $usuario->permissoes($empresa->codempresa);
      $dadosusuario['empresa'] =  $empresa->toCompleteArray();

      $ret->data = [
          'usuario' => $dadosusuario,
          'token' => $tokenuser
      ];
      $ret->ok = true;

    } catch (\Throwable $th) {
      $ret->msg = $th->getMessage();
      return $ret->toJson();
    }
    return $ret->toJson();
  }

}
