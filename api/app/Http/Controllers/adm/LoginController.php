<?php

namespace App\Http\Controllers\adm;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Exception;
use App\models\User;

class LoginController extends Controller
{
  public function index(Request $request)  {
    if (Auth::guard('adm')->check()) {
      return redirect()->route('adm.home');
    };
    return view('adm.auth.login');
  }

  public function logout()  {
    Auth::guard('adm')->logout();
    return redirect()->route('adm.login');
  }

  public function login(Request $request)  {
    try {
      if (Auth::guard('adm')->check()) {
        return redirect()->route('adm.home');
      };

      $login = isset($request->login) ? $request->login : '';
      $password = isset($request->password) ? $request->password : '';

      $usuario = User::where('login', '=', $login)->first();
      if (!$usuario) throw new Exception('Nenhum usuário encontrado');
      if ($usuario->ativo != 1) throw new Exception('Usuário inativo');
      // if ($usuario->ativo != 1) throw new Exception('Usuário inativo');

      // mysqlPassword
      $credencial = [
        'login' => $usuario->login,
        'password' => mysqlPassword($password),
        // 'passwordmd' => $usuario->senha
      ];

      if (Auth::guard('adm')->attempt($credencial)) {
        return redirect()->route('adm.home');
      } else {
        throw new Exception('Não autenticado');
      }

    } catch (Exception $e) {
      return back()->withErrors($e->getMessage());
    }
  
  }

}
