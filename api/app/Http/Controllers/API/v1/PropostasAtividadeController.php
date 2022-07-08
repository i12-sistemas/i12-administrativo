<?php

namespace App\Http\Controllers\API\v1;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\models\PropostaAtividade;
use App\Http\Controllers\RetornoApiController;

use Exception;


class PropostasAtividadeController extends Controller
{
    public function index($id)
    {   
        $proposta = PropostaAtividade::where('id', $id)
                    ->first();
        $proposta->apontamentos;
        // $fases = [];
        // if (isset($proposta)){
        //     if ($proposta->atividades->count() > 0){
        //         $fases = $proposta->atividades->sortBy('nitem')->sortBy('fase')->groupBy('fase');
        //         foreach($proposta->atividades as $ativ){
        //             $ativ->apontamentos;
        //         }
        //     }
        // }
        return $proposta;
    }

    public function updatestatus(Request $request)
    {  
        $ret = new RetornoApiController;
        try{
            $dataForm = $request->all();
            $ids = trim(((isset($dataForm['ids']) ? $dataForm['ids'] : '')));
            if ($ids==''){
                $ids = [];
            }else{
                $ids = explode(",", $ids);
            }
            if (count($ids)<=0) {
                throw new Exception('Nenhum ID informado.'); 
            }
            $newids = [];
            foreach($ids as $id){
                if (!(is_numeric($id))) {
                    throw new Exception('ID inválido. [ID: ' . $id. ']'); 
                }
                $id=intval($id);
                if (!($id > 0)) {
                    throw new Exception('ID inválido. [ID: ' . $id. ']'); 
                }
                $newids[] = $id;
            }
            if (count($newids)<=0) {
                throw new Exception('Nenhum ID informado.'); 
            }
  
            
            $valor = (isset($dataForm['valor']) ? $dataForm['valor'] : '');
            if (!(is_numeric($valor))) {
                throw new Exception('Valor inválido. Valor informado ' . $valor); 
            }
            $valor = intval($valor);
            if (!(($valor==0) or ($valor==50) or ($valor==100))) {
                throw new Exception('Valor deve ser 0, 50 ou 100. Valor informado ' . $valor); 
            }
        
            
            $atividades = PropostaAtividade::whereIn('id', $ids)->get();
            if ($atividades->isEmpty()) {
                throw new Exception('Nenhuma atividade encontrada'); 
            }
     
        
            DB::beginTransaction();

            $idsupdated = [];
            foreach($atividades as $atividade){
                $atividade->apontencerrado = $valor;
                $upd = $atividade->save();
                if (!$upd){
                    throw new Exception('Status não foi salvo!'); 
                }
                $idsupdated[] = $atividade->id;
            }
    
            DB::commit();
            $ret->rows = $idsupdated;
            $ret->ok = true;
            $ret->msg = 'Status atualizado com sucesso!';
            
        } catch (Exception $e) {
            DB::rollBack();
            $ret->ok = false;
            $ret->msg = 'Falha ao atualizar status - ' . $e->getMessage() ;
        }        
       

         return $ret->toJson();
    }
    public function retrabalho(Request $request)
    {   
        $ret = new RetornoApiController;
        try{
            $dataForm = $request->all();
            $ids = trim(((isset($dataForm['ids']) ? $dataForm['ids'] : '')));
            if ($ids==''){
                $ids = [];
            }else{
                $ids = explode(",", $ids);
            }
            if (count($ids)<=0) {
                throw new Exception('Nenhum ID informado.'); 
            }
            $newids = [];
            foreach($ids as $id){
                if (!(is_numeric($id))) {
                    throw new Exception('ID inválido. [ID: ' . $id. ']'); 
                }
                $id=intval($id);
                if (!($id > 0)) {
                    throw new Exception('ID inválido. [ID: ' . $id. ']'); 
                }
                $newids[] = $id;
            }
            if (count($newids)<=0) {
                throw new Exception('Nenhum ID informado.'); 
            }
  
            
            $valor = (isset($dataForm['valor']) ? $dataForm['valor'] : '');
            if (!(is_numeric($valor))) {
                throw new Exception('Valor inválido. Valor informado ' . $valor); 
            }
            $valor = intval($valor);
            if (!(($valor==0) or ($valor==1))) {
                throw new Exception('Valor deve ser 0 ou 1. Valor informado ' . $valor); 
            }
        
            $atividades = PropostaAtividade::whereIn('id', $ids)->get();
            if ($atividades->isEmpty()) {
                throw new Exception('Nenhuma atividade encontrada'); 
            }
     
        
            DB::beginTransaction();

            $idsupdated = [];
            foreach($atividades as $atividade){
                $atividade->apontretrabalho = $valor;
                $upd = $atividade->save();
                if (!$upd){
                    throw new Exception('Status não foi salvo!'); 
                }
                $idsupdated[] = $atividade->id;
            }
    
            DB::commit();
            $ret->rows = $idsupdated;
            $ret->ok = true;
            $ret->msg = 'Retrabalho atualizado com sucesso!';
            
        } catch (Exception $e) {
            DB::rollBack();
            $ret->ok = false;
            $ret->msg = 'Falha ao atualizar retrabalho - ' . $e->getMessage() ;
        }        
       

         return $ret->toJson();
    }   
    
    
}
