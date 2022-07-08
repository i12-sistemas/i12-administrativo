<?php

namespace App\Http\Controllers\API\v1\painelcliente;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\models\Configuracao;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;
use App\Http\Controllers\RetornoApiController;

class ConfiguracaoController extends Controller
{

    public function AddorUpdate($flagid, $tipo, $valor){
      
        $texto = '';
        $value = '';
             
        switch ($tipo) {
            case 'JSON':
                $texto = json_encode($valor);
                break;
            case 'TEXTO':
                $texto = base64_decode($valor);
                break;
            case 'DATAHORA':
                try{
                    if (!Carbon::createFromFormat('Y-m-d h:i:s', $valor)) {
                        throw new Exception('Data de nascimento inválida. ' . $dados->dtnasc);
                    }else{
                        $value = Carbon::createFromFormat('Y-m-d h:i:s', $valor)->format('Y-m-d h:i:s');
                    }
                } catch (Exception $e) {
                    $value = '';
                }            
                
                break;
            case 'DATA':
                try{
                    if (!Carbon::createFromFormat('Y-m-d', $valor)) {
                        throw new Exception('Data de nascimento inválida. ' . $dados->dtnasc);
                    }else{
                        $value = Carbon::createFromFormat('Y-m-d', $valor)->format('Y-m-d');
                    }
                } catch (Exception $e) {
                    $value = '';
                }            
                
                break;                
            case 'LOGICO':
                $value = (($valor=='1')||($valor==1)||($valor)||($valor=='S')||($valor=='s'));
                $value = ($value ? 1: 0);
                break;
            default:
                $value =  $valor;
        }   
       
        $config = Configuracao::find($flagid);
        if(!$config){
            $config = new Configuracao;
            $config->flagid = $flagid;
        }
        $config->tipocampo = $tipo;
        $config->valor = $value;
        $config->texto = $texto;
        return $config->save();
    }

    
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
            $ret->ok = true;
            $ret->rows = $config;
        } catch (Exception $e) {
            $ret->ok = false;
            $ret->msg = $e->getMessage() ;

        }               
        return $ret->toJson();
    }

    public function save(Request $request){
        $ret = new RetornoApiController;
        try{

            $aConfig = isset($request->dados) ? $request->dados : '';
            $dados = json_decode($aConfig);

            DB::beginTransaction();

            foreach($dados as $key => $c){
                // dd($c->valor);
                
                
                $upd = $this->AddorUpdate($key, $c->tipo, $c->valor);
                if (!$upd){
                    throw new Exception('Configuração não foi salva!'); 
                }                      
            }

            DB::commit();

            $ret->ok = true;
        } catch (Exception $e) {
            DB::rollBack();
            $ret->ok = false;
            $ret->msg = $e->getMessage() ;
        }               
        return $ret->toJson();
    }    
}
