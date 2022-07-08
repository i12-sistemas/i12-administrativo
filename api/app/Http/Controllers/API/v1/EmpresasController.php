<?php

namespace App\Http\Controllers\API\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\models\Empresa;
use App\Http\Controllers\RetornoApiController;
use Illuminate\Support\Facades\DB;
use Exception;
use App\models\ClientesCaract;
use Carbon\Carbon;
use App\models\Clientes;
use App\models\ClientesCaractValor;

class EmpresasController extends Controller
{
    public function list(Request $request)
    {   
        $ativo = (isset($request->ativo) ? intval($request->ativo) : 1);
        if (!(($ativo==1)or($ativo==0))){
            $ativo=1;
        }
        $empresa = Empresa::select('id', 'fantasia', 'razao', 'cnpj', 'nomecurto', 'ativo')
                    ->where('ativo', $ativo)
                    ->get();
        return $empresa;
    }    


    public function versaoupdate(Request $request){

        
        $ret = new RetornoApiController;
        try{
            $cnpjempresa = isset($request->cnpjempresa) ? $request->cnpjempresa : '';
            if($cnpjempresa=='')  throw new Exception("CNPJ da empresa inválido " . $cnpjempresa);
            $cnpjempresa = explode(",", $cnpjempresa);
            if(count($cnpjempresa)==0)  throw new Exception("CNPJ da empresa inválido " . $cnpjempresa);
            //$empresacliente = Clientes::whereRaw('(cnpj=? or cpf=? or concat("000", ifnull(cpf,""))=?)', [$cnpjempresa, $cnpjempresa, $cnpjempresa])->first();
            $empresas = Clientes::whereIn(DB::raw('if(pessoa="J", cnpj, concat("000", ifnull(cpf,"")))'), $cnpjempresa  )->get();
            if(!$empresas) throw new Exception('Empresa não foi localizada com o documento ' . $cnpjempresa);
            $SISTEMAEXE = isset($request->SISTEMAEXE) ? $request->SISTEMAEXE : '';
            $VERSAODATABASE = isset($request->VERSAODATABASE) ? $request->VERSAODATABASE : '';

            $ICOM_MODULO_VENDAS = isset($request->ICOM_MODULO_VENDAS) ? $request->ICOM_MODULO_VENDAS : '';
            $ICOM_MULTIEMPRESA = isset($request->ICOM_MULTIEMPRESA) ? $request->ICOM_MULTIEMPRESA : '';
            $ICOM_MODULO_VENDAS_CONDICIONAL = isset($request->ICOM_MODULO_VENDAS_CONDICIONAL) ? $request->ICOM_MODULO_VENDAS_CONDICIONAL : '';
            $ICOM_MODULO_SERVICO = isset($request->ICOM_MODULO_SERVICO) ? $request->ICOM_MODULO_SERVICO : '';
            $ICOM_MODULO_CONTRATO = isset($request->ICOM_MODULO_CONTRATO) ? $request->ICOM_MODULO_CONTRATO : '';
            $ICOM_MODULO_NFE = isset($request->ICOM_MODULO_NFE) ? $request->ICOM_MODULO_NFE : '';
            $ICOM_MODULO_CFE = isset($request->ICOM_MODULO_CFE) ? $request->ICOM_MODULO_CFE : '';
            $ICOM_MODULO_NFSE = isset($request->ICOM_MODULO_NFSE) ? $request->ICOM_MODULO_NFSE : '';
            $ICOM_MODULO_SAPV = isset($request->ICOM_MODULO_SAPV) ? $request->ICOM_MODULO_SAPV : '';
            $ICOM_MODULO_FOLHAPGTO = isset($request->ICOM_MODULO_FOLHAPGTO) ? $request->ICOM_MODULO_FOLHAPGTO : '';
            $ICOM_MODULO_RDV = isset($request->ICOM_MODULO_RDV) ? $request->ICOM_MODULO_RDV : '';
            $ICOM_MODULO_FIN_CONTACORRENTE = isset($request->ICOM_MODULO_FIN_CONTACORRENTE) ? $request->ICOM_MODULO_FIN_CONTACORRENTE : '';
            $ICOM_MODULO_FIN_REC_CARTAO = isset($request->ICOM_MODULO_FIN_REC_CARTAO) ? $request->ICOM_MODULO_FIN_REC_CARTAO : '';
            $ICOM_MODULO_FIN_REC_CHEQUETERC = isset($request->ICOM_MODULO_FIN_REC_CHEQUETERC) ? $request->ICOM_MODULO_FIN_REC_CHEQUETERC : '';
            $ICOM_MODULO_FIN_PAG_CHEQUEPROPRIO = isset($request->ICOM_MODULO_FIN_PAG_CHEQUEPROPRIO) ? $request->ICOM_MODULO_FIN_PAG_CHEQUEPROPRIO : '';
            $ICOM_MODULO_PRODUTOS_TIPO = isset($request->ICOM_MODULO_PRODUTOS_TIPO) ? $request->ICOM_MODULO_PRODUTOS_TIPO : '';
            $ICOM_MODULO_VENDAS_TIPO = isset($request->ICOM_MODULO_VENDAS_TIPO) ? $request->ICOM_MODULO_VENDAS_TIPO : '';


            
 
        

            $caracteriscas = [  'SISTEMAEXE' => $SISTEMAEXE, 
                                'VERSAODATABASE' => $VERSAODATABASE,

                                'ICOM_MODULO_VENDAS' => $ICOM_MODULO_VENDAS,
                                'ICOM_MULTIEMPRESA' => $ICOM_MULTIEMPRESA,
                                'ICOM_MODULO_VENDAS_CONDICIONAL' => $ICOM_MODULO_VENDAS_CONDICIONAL,
                                'ICOM_MODULO_SERVICO' => $ICOM_MODULO_SERVICO,
                                'ICOM_MODULO_CONTRATO' => $ICOM_MODULO_CONTRATO,
                                'ICOM_MODULO_NFE' => $ICOM_MODULO_NFE,
                                'ICOM_MODULO_CFE' => $ICOM_MODULO_CFE,
                                'ICOM_MODULO_NFSE' => $ICOM_MODULO_NFSE,
                                'ICOM_MODULO_SAPV' => $ICOM_MODULO_SAPV,
                                'ICOM_MODULO_FOLHAPGTO' => $ICOM_MODULO_FOLHAPGTO,
                                'ICOM_MODULO_RDV' => $ICOM_MODULO_RDV,
                                'ICOM_MODULO_FIN_CONTACORRENTE' => $ICOM_MODULO_FIN_CONTACORRENTE,
                                'ICOM_MODULO_FIN_REC_CARTAO' => $ICOM_MODULO_FIN_REC_CARTAO,
                                'ICOM_MODULO_FIN_REC_CHEQUETERC' => $ICOM_MODULO_FIN_REC_CHEQUETERC,
                                'ICOM_MODULO_FIN_PAG_CHEQUEPROPRIO' => $ICOM_MODULO_FIN_PAG_CHEQUEPROPRIO,
                                'ICOM_MODULO_PRODUTOS_TIPO' => $ICOM_MODULO_PRODUTOS_TIPO,
                                'ICOM_MODULO_VENDAS_TIPO' => $ICOM_MODULO_VENDAS_TIPO,

                            ];
        } catch (Exception $e) {
            $ret->msg = $e->getMessage();
            return $ret->toJson();
        }


        try{
            DB::beginTransaction();

            foreach ($empresas as $empresa) {
              foreach ($caracteriscas as $key => $value) {
                  $carac = ClientesCaract::where('carac', $key)->first();
                  if($carac){
                      $reg = ClientesCaractValor::where('idcliente', $empresa->codcliente)->where('idcaract', $carac->id)->first();
                      if(!$reg){
                          $reg = new ClientesCaractValor;
                          $reg->idcliente  = $empresa->codcliente;
                          $reg->idcaract  = $carac->id;
                      }else{
                          if($carac->permitemultivalor==1){
                              $reg = new ClientesCaractValor;
                              $reg->idcliente  = $empresa->codcliente;
                              $reg->idcaract  = $carac->id;                            
                          }
                      }
                      $reg->dhupdate = Carbon::now();
                      if($carac->tipo=='string'){
                          $reg->vstring = $value;
                      }else if($carac->tipo=='integer'){
                          $reg->vint = $value;
                      }else if($carac->tipo=='double'){
                          $reg->vdouble = $value;
                      }else if($carac->tipo=='boolean'){
                          $reg->vint = (($value==1)||($value=='1')||($value=='S')||($value==true) ? 1 : 0);
                      }
                      $saved = $reg->save();
                      if (!$saved){
                          throw new Exception('Falha ao salvar caracteristica!'); 
                      }

                  }
              }
            }
            
            DB::commit();
            $ret->ok = true;
        } catch (Exception $e) {
            DB::rollBack();
            $ret->msg = $e->getMessage();
        }           
        return $ret->toJson();

    }
}
