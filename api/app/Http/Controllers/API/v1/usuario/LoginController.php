<?php

namespace App\Http\Controllers\API\v1\usuario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function resetpassword(Request $request)
    {

        $ret = new RetornoApiController;
        try {

            $iduser = (isset($request->iduser) ? intval($request->iduser) : 0);
            $idassociado = (isset($request->idassociado) ? intval($request->idassociado) : 0);

            $associado = Associado::find($idassociado);
            if (!$associado) {
                throw new Exception('Associado não foi encontrado!');
            }

            $usuario = User::find($iduser);
            if (!$usuario) {
                throw new Exception('Usuário não foi encontrado!');
            }

            $linkacesso = route('usuario.login');
            $site = route('site.home');

            $empresa = Empresa::first();

        } catch (Exception $e) {
            $ret->ok = false;
            $ret->msg = $e->getMessage();
            return $ret->toJson();
        }

        $dados = ['associado' => $associado,
            'usuario' => $usuario,
            'empresa' => $empresa,
            'linkacesso' => $linkacesso,
            'site' => $site,
        ];
        try {
            Mail::send(new SendLogin($dados));
            $ret->ok = true;
        } catch (Exception $e) {
            $ret->msg = $e->getMessage();
        }

        return $ret->toJson();
    }
}
