<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use App\models\User;
use App\models\Clientes;
use Illuminate\Support\Facades\DB;

class BasicAuthiCom
{

    public function handle($request, Closure $next)
    {   
        header('Cache-Control: no-cache, must-revalidate, max-age=0');
        try{
            // $has_supplied_credentials = !(empty($_SERVER['PHP_AUTH_USER']) && empty($_SERVER['PHP_AUTH_PW']));
            $user = isset($_SERVER['PHP_AUTH_USER']) ? $_SERVER['PHP_AUTH_USER'] : '';
            $pw = isset($_SERVER['PHP_AUTH_PW']) ? $_SERVER['PHP_AUTH_PW'] : '';
            $cnpj = isset($_SERVER['HTTP_HCNPJ']) ? $_SERVER['HTTP_HCNPJ'] : '';
            $sistema = isset($_SERVER['HTTP_HSISTEMA']) ? $_SERVER['HTTP_HSISTEMA'] : '';
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

            $empresa = Clientes::whereRaw('if(clientes.pessoa="J", clientes.cnpj=?, concat("000",clientes.cpf)=?)', [$cnpj, $cnpj])->first();
            if(!$empresa) throw new Exception('003-empresa-nao-localizada');
            if(!$empresa->ativo==1) throw new Exception('003-empresa-desativada');
            
            
        } catch (Exception $e) {
            print_r($e->getMessage());
            header('HTTP/1.1 401 Authorization Required');
            header('errorcode: "' . $e->getMessage() . '"');
            exit;
        } 

        return $next($request);
        
    }
}
