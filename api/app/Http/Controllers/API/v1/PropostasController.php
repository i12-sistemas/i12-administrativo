<?php

namespace App\Http\Controllers\API\v1;

use App\models\Propostas;
use Illuminate\Http\Request;
use Exception;
use App\Http\Controllers\Controller;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use App\models\Sharedprojeto;
use Carbon\Carbon;
use App\Http\Controllers\RetornoApiController;

class PropostasController extends Controller
{

    public function fases($numproposta)
    {   
        $proposta = Propostas::where('status', 'GANHO')
                    ->where('numproposta', $numproposta)
                    ->first();
        $fases = [];
        if (isset($proposta)){
            if ($proposta->atividades->count() > 0){
                $fases = $proposta->atividades->sortBy('nitem')->sortBy('fase')->groupBy('fase');
                foreach($proposta->atividades as $ativ){
                    $ativ->apontamentos;
                }
            }
        }

         return $fases;
    }

    public function projetos(Request $request)
    {   
        $statusprojeto = trim(((isset($request->statusprojeto) ? $request->statusprojeto : '')));
        $idempresa = (isset($request->idempresa) ? intval($request->idempresa) : 0);
        $limit = (isset($request->limit) ? intval($request->limit) : -1);
        $projetos = Propostas::where('status', 'GANHO')
                    ->whereRaw('if(?<>"", statusprojeto=?, true)', [$statusprojeto, $statusprojeto])
                    ->whereRaw('if(?>0, idempresaresp=?, true)', [$idempresa, $idempresa])
                    ->orderByRaw('dtemissao asc')
                    ->limit($limit)
                    ->get();
        
        foreach($projetos as $key => $projeto){
            $projeto->cliente->id;
            $projeto->cliente->nomeexibicao;
            $projeto->atividades;
        }
        
         return $projetos;
    }  
    
    public function sharing($numprojeto, Request $request)
    {   
        $showall = (isset($request->showall) ? $request->showall : false);
        
        
        $projeto = Propostas::select('id', 'numproposta', 'nomeprojeto')
                    ->where('status', 'GANHO')
                    ->where('numproposta', $numprojeto)
                    ->first();
        if(!$showall){
            $shares  = $projeto->sharingativos;
        }else{
            $shares  = $projeto->sharing;
        }
        
        foreach($shares as $share){
            $share->leituras;
        }

         return [   'id' => $projeto->id, 
                    'nomeprojeto'   =>  $projeto->nomeprojeto,
                    'numproposta'   =>  $projeto->numproposta,
                    'sharing'       =>  $shares
                ];
    }      
    
    public function shareadd($numprojeto, Request $request)
    {   
        $ret = new RetornoApiController;
        try{
            $userid = (isset($request->userid) ? intval($request->userid) : -1);
            $email = (isset($request->email) ? $request->email : '');
            $nomecontato = (isset($request->nomecontato) ? $request->nomecontato : '');
            $dhvalidade = (isset($request->dhvalidade) ? $request->dhvalidade : '');

            $projeto = Propostas::select('id', 'numproposta', 'nomeprojeto')
                        ->where('status', 'GANHO')
                        ->where('numproposta', $numprojeto)
                        ->first();

            if(!$projeto){
                throw new Exception('Projeto não foi encontrado!'); 
            }                                    
        } catch (Exception $e) {
            $ret->ok = false;
            $ret->msg = $e->getMessage() ;
            return $ret->toJson();
        }           
        
        try{
            DB::beginTransaction();

            $share = new Sharedprojeto;
            $share->idprojeto = $projeto->id;
            $share->hash = '';
            $share->email = $email;
            $share->nomecontato = $nomecontato;
            $share->dhcreate = Carbon::now();
            $share->idusercreate = $userid;
            $share->dhvalidade = $dhvalidade;
            $saved = $share->save();
            if (!$saved){
                throw new Exception('Falha ao adicionar compartilhamento!'); 
            }
            $newid = $share->id;
            DB::commit();

            $sharenew = Sharedprojeto::select('hash')->find($newid);
            $ret->ok = true;
            $ret->id = $sharenew->hash ;
        } catch (Exception $e) {
            DB::rollBack();
            $ret->ok = false;
            $ret->msg = $e->getMessage() ;
        }
         return $ret->toJson();
    } 

    public function sharerevogar($numprojeto, Request $request)
    {   
        $ret = new RetornoApiController;
        try{
            $userid = (isset($request->userid) ? intval($request->userid) : -1);
            $hash = (isset($request->hash) ? $request->hash : '');

            $projeto = Propostas::select('id', 'numproposta', 'nomeprojeto')
                        ->where('status', 'GANHO')
                        ->where('numproposta', $numprojeto)
                        ->first();

            if(!$projeto){
                throw new Exception('Projeto não foi encontrado!'); 
            }                                    

            $share = Sharedprojeto::where('hash', $hash)->where('idprojeto', $projeto->id)->first();
            if(!$share){
                throw new Exception('Compartilhamento não foi encontrado!'); 
            }
            if($share->revogado == 1){
                throw new Exception('Compartilhamento já está revogado!  ****'); 
            }            

        } catch (Exception $e) {
            $ret->ok = false;
            $ret->msg = $e->getMessage() ;
            return $ret->toJson();
        }           
        
        try{
            DB::beginTransaction();

            $share->iduserrevogacao = $userid;
            $share->dhrevogacao = Carbon::now();
            $share->revogado = 1;
            
            $saved = $share->save();
            if (!$saved){
                throw new Exception('Falha ao revogar compartilhamento!'); 
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

    public function listastatusprojetos()
    {   
        $proposta = Propostas::select('statusprojeto', DB::raw("count(distinct id) as qtde"))
                    ->where('status', 'GANHO')
                    ->groupBy('statusprojeto')
                    ->get();

         return $proposta;
    }   
    



}
