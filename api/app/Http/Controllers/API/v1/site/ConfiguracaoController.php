<?php

namespace App\Http\Controllers\API\v1\site;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\models\Configuracao;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;
use App\Http\Controllers\RetornoApiController;

class ConfiguracaoController extends Controller
{
    public function getconfig(Request $request){
        $ret = new RetornoApiController;
        try{
            $FLAGIDS = isset($request->FLAGIDS) ? $request->FLAGIDS : '';
            $list = json_decode($FLAGIDS);
            
            

            if(count($list)==0){
                throw new Exception('Nenhuma configuração solicitada.'); 
            };

            $config = [];
            foreach($list as $id){
                $configuracao = Configuracao::find($id);
                if($configuracao){
                    $config = array_add($config, $id, $configuracao->toArray());
                }
            }
            
            return $config;
        } catch (Exception $e) {
            $ret->ok = false;
            $ret->msg = $e->getMessage() ;
            return $ret->toJson();
        }               
    }

}
