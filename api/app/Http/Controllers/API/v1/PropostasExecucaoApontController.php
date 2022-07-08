<?php

namespace App\Http\Controllers\API\v1;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\models\PropostasExecucaoApont;
use App\models\PropostaAtividade;
use App\models\Configuracao;
use App\Http\Controllers\RetornoApiController;
use Illuminate\Support\Facades\Validator;

use Exception;

class PropostasExecucaoApontController extends Controller
{
    public function deleteapont(Request $request)
    {  
        $ret = new RetornoApiController;
        try{
            $ids = trim((isset($request->ids) ? $request->ids : ''));
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

 
             $atividades = PropostasExecucaoApont::whereIn('id', $ids)->get();
            if (!$atividades) {
                throw new Exception('Nenhuma atividade encontrada'); 
            }



            
        
            DB::beginTransaction();

            $idsupdated = [];
            foreach($atividades as $atividade){
                $ativCheck = PropostaAtividade::find($atividade->idatividade);
                if (!$ativCheck) {
                    throw new Exception('Nenhuma atividade relacionada ao apontamento.'); 
                }
                if ($ativCheck->apontencerrado>=100) {
                    throw new Exception('Atividade já foi encerrada!'); 
                }            
            

                $del = $atividade->delete();
                if (!$del){
                    throw new Exception('Status não foi salvo!'); 
                }
                $idsdeleted[] = $atividade->id;
            }
    
            DB::commit();
            $ret->rows = $idsdeleted;
            $ret->ok = true;
            $ret->msg = 'Registro deletado com sucesso!';
            
        } catch (Exception $e) {
            DB::rollBack();
            $ret->ok = false;
            $ret->msg = $e->getMessage() ;
        }        
       

         return $ret->toJson();
    }   

    public function insertapont(Request $request)
    {  
        $ret = new RetornoApiController;
        try{

            $rules = ['data' => 'required|date',
                'turno' => 'required',
                'minuto' => 'required|integer|between:1,1439',
                'idatividade' => 'required|integer',
                'idconsultor' => 'required|integer'
            ];
            $messages = [
                'required'    => 'Campo :attribute é obrigatório.',
                'integer'    => 'Campo :attribute deve ser um número inteiro.',
                'date'    => 'Campo :attribute deve ser uma data',
                'between'    => 'Campo :attribute deve ser informado entre :min a :max.',
            ];            

            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                $e = [];
                foreach($validator->errors()->all() as $error){
                    $e[] = $error;
                }
                $ret->rows = $e;
                throw new Exception(count($validator->errors()) . ' erro(s) encontrado(s).');
            }            

            $data = (isset($request->data) ? $request->data : '');
            $turno = (isset($request->turno) ? $request->turno : '');
            $minuto = (isset($request->minuto) ? intval($request->minuto) : 0);
            $idatividade = (isset($request->idatividade) ? intval($request->idatividade) : 0);
            $idconsultor = (isset($request->idconsultor) ? intval($request->idconsultor) : 0);
            $peso = 1;

            $turno_peso = Configuracao::find($turno . "_PESO");
            $peso = strtoFloat($turno_peso->valor);
            
            // $data = (isset($request->data) ? $request->data : '');
            
            // if (count($newids)<=0) {
            //     throw new Exception('Nenhum ID informado.'); 
            // }
  
            $atividade = PropostaAtividade::find($idatividade);
            if (!$atividade) {
                throw new Exception('Nenhuma atividade encontrada'); 
            }
            if ($atividade->apontencerrado>=100) {
                throw new Exception('Atividade já foi encerrada!'); 
            }            
        
            DB::beginTransaction();

            $apont = new PropostasExecucaoApont;
            $apont->idatividade = $idatividade;
            $apont->idconsultor = $idconsultor;
            $apont->dataexec = $data;
            $apont->turno = $turno;
            $apont->tempomin = $minuto;
            $apont->total = $minuto * $peso;
            $retSave = $apont->save();
            if (!$retSave){
                throw new Exception('Falha ao incluir apontamento! - ' .  $retSave ); 
            }


            $sql = "call p_proposta_totalizaGestao(?)";
            DB::select($sql, [$atividade->idproposta]);            
            
            
            DB::commit();
            $ret->id = $apont->id;
            $ret->ok = true;
            $ret->msg = 'Registro inserido com sucesso!';
            
        } catch (Exception $e) {
            DB::rollBack();
            $ret->ok = false;
            $ret->msg = $e->getMessage() ;
        }        
       

         return $ret->toJson();
    }     
}
