<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use App\models\Contato;
use App\models\Clientes;
use Illuminate\Support\Facades\DB;

class BashAuthUserRelacionamento
{

    public function handle($request, Closure $next)
    {   
        
        //HTTP_TESTEHEADER
        
        header('Cache-Control: no-cache, must-revalidate, max-age=0');
        try{
            // $has_supplied_credentials = !(empty($_SERVER['PHP_AUTH_USER']) && empty($_SERVER['PHP_AUTH_PW']));
            //user = idrelacioamento
            $user = isset($_SERVER['PHP_AUTH_USER']) ? $_SERVER['PHP_AUTH_USER'] : '';
            $pw = isset($_SERVER['PHP_AUTH_PW']) ? $_SERVER['PHP_AUTH_PW'] : '';
            $cnpj = isset($_SERVER['HTTP_CNPJ']) ? $_SERVER['HTTP_CNPJ'] : '';
            $cnpjLista = explode(',', $cnpj);
            $sistema = isset($_SERVER['HTTP_SISTEMA']) ? $_SERVER['HTTP_SISTEMA'] : '';
            if ($user==''){
                throw new Exception('001-no-user');
            }
            if ($pw==''){
                throw new Exception('002-no-password');
            }
            if ($cnpj=='') throw new Exception('002-nenhum-cnpj-informado');
            if (count($cnpjLista)<=0) throw new Exception('003-nenhum-cnpj-informado');
            
            if ($sistema==''){
                throw new Exception('002-no-sistema');
            }
            $pwcheck = md5($user . '_cnpj' . $cnpj . '_sistema' . $sistema);

            if (!($pw==$pwcheck)){
                throw new Exception('003-falha-na-autenticacao');
            }

            $contato = Contato::find($user);
            if(!$contato) throw new Exception('004-usuario-de-relacionamento-nao-localizado');
            if(!$contato->lixeira==0) throw new Exception('004-usuario-de-relacionamento-inativo');

            $cnpjJoin = "'" . implode("','", $cnpjLista)."'";
            $empresas = Clientes::where('ativo', 1)
                                ->whereRaw('((cnpj in (' . $cnpjJoin . ')) or (concat("000",cpf) in (' . $cnpjJoin . ')))')
                                ->get();
            
            if(!$empresas) throw new Exception('003-empresa-nao-localizada');
            
        } catch (Exception $e) {
            print_r($e->getMessage());
            header('HTTP/1.1 401 Authorization Required');
            header('errorcode: "' . $e->getMessage() . '"');
            exit;
        } 

        return $next($request);
        
    }
}
