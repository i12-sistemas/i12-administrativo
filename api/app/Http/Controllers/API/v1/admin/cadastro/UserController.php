<?php

namespace App\Http\Controllers\API\v1\admin\cadastro;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\models\User;
use Illuminate\Support\Facades\DB;
use Exception;
use App\Http\Controllers\RetornoApiController;


class UserController extends Controller
{
    public function show(Request $request)
    {   
        $ret = new RetornoApiController;
        try{
            $id = (isset($request->id) ? intval($request->id) : 0);
            $user = User::select('id', 'nome', 'email', 'foto')->find($id);
            if (!$user) {
                throw new Exception('Nenhum usuário encontrado.'); 
            }
           return $user;
            
        } catch (Exception $e) {
            $ret->ok = false;
            $ret->msg = $e->getMessage() ;
            return $ret->toJson();
        }            
        
    }   
}
