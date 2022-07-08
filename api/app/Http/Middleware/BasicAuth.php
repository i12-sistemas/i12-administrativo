<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use App\models\User;
use Illuminate\Support\Facades\DB;

class BasicAuth
{

    public function handle($request, Closure $next)
    {   
        
        //HTTP_TESTEHEADER
        
        header('Cache-Control: no-cache, must-revalidate, max-age=0');
        try{
            // $has_supplied_credentials = !(empty($_SERVER['PHP_AUTH_USER']) && empty($_SERVER['PHP_AUTH_PW']));
            $user = isset($_SERVER['PHP_AUTH_USER']) ? $_SERVER['PHP_AUTH_USER'] : '';
            $pw = isset($_SERVER['PHP_AUTH_PW']) ? $_SERVER['PHP_AUTH_PW'] : '';
            $cnpj = isset($_SERVER['HTTP_CNPJ']) ? $_SERVER['HTTP_CNPJ'] : '';
            $sistema = isset($_SERVER['HTTP_SISTEMA']) ? $_SERVER['HTTP_SISTEMA'] : '';
            if ($user==''){
                throw new Exception('001-no-user');
            }
            if ($pw==''){
                throw new Exception('002-no-password');
            }
            if ($cnpj==''){
                throw new Exception('002-no-cnpj');
            }
            if ($sistema==''){
                throw new Exception('002-no-sistema');
            }
            $pwcheck = md5($user . '_cnpj' . $cnpj . '_sistema' . $sistema);

            if (!($pw==$pwcheck)){
                throw new Exception('003-falha-na-autenticacao');
            }

            $sql = 'select id, situacao from empresa where cnpj=?';
            // dd($sql);
            $empresa = DB::connection('mysql2')->select($sql, [$cnpj]);
            if(!$empresa) throw new Exception('003-empresa-nao-localizada');
            if(!$empresa[0]->situacao==1) throw new Exception('003-empresa-desativada');
            
            
        } catch (Exception $e) {
            print_r($e->getMessage());
            header('HTTP/1.1 401 Authorization Required');
            header('errorcode: "' . $e->getMessage() . '"');
            exit;
        } 

        return $next($request);
        
    }
}
