<?php

namespace App\Http\Controllers\API\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\RetornoApiController;
use Exception;
use App\models\Propostas;
use Carbon\Carbon;
use App\models\ClienteReadProjeto;
use Illuminate\Support\Facades\Mail;
use App\Mail\StatusReportCliente;
use App\Sharedprojeto;

class ClienteReadProjetoController extends Controller
{

    public function sendmailstatusreport(Request $request)
    {
    
        $ret = new RetornoApiController;
        try{
            
            $numprojeto = (isset($request->numprojeto) ? $request->numprojeto : '');
            if($numprojeto==''){
                throw new Exception('Número do projeto inválido!'); 
            }

            $hash = (isset($request->hash) ? $request->hash : '');
            if($hash==''){
                throw new Exception('Hash inválido!'); 
            }

            $projeto = Propostas::where('status', 'GANHO')
                        ->where('numproposta', $numprojeto)
                        ->first();
            if(!$projeto){
                throw new Exception('Projeto não foi encontrado!'); 
            }

            $share = Sharedprojeto::where('hash', $hash)->where('idprojeto', $projeto->id)->first();
            if(!$share){
                throw new Exception('Compartilhamento não foi encontrado!'); 
            }

            // $projeto->empresa;

        } catch (Exception $e) {
            $ret->ok = false;
            $ret->msg = $e->getMessage() ;
            return $ret->toJson();
        }       

        $dados = ['projeto' => $projeto, 'share' => $share];
        try {
            Mail::send(new StatusReportCliente($dados));
            $ret->ok = true;
        } catch (Exception $e) {
            $ret->msg = $e->getMessage();
        }

        return $ret->toJson();
    }

    public function readstatusreport(Request $request){
        $ret = new RetornoApiController;
        try{
            
            $numprojeto = (isset($request->numprojeto) ? $request->numprojeto : '');
            if($numprojeto==''){
                throw new Exception('Número do projeto inválido!'); 
            }

            $hash = (isset($request->hash) ? $request->hash : '');
            if($hash==''){
                throw new Exception('Hash inválido!'); 
            }

            $token = (isset($request->token) ? $request->token : '');
            if($token==''){
                throw new Exception('Token inválido!'); 
            }            

            $projeto = Propostas::select('propostas.id')
                        ->where('status', 'GANHO')
                        ->where('numproposta', $numprojeto)
                        ->first();
            if(!$projeto){
                throw new Exception('Projeto não foi encontrado!'); 
            }

            $leituraupdate = ClienteReadProjeto::where('session', $token)->where('hash', $hash)->first();
            if(!$leituraupdate){
                throw new Exception('Leitura anterior não foi encontrado!'); 
            }            
            

        } catch (Exception $e) {
            $ret->ok = false;
            $ret->msg = $e->getMessage() ;
            return $ret->toJson();
        }        
          
        try{
            DB::beginTransaction();

            $leituraupdate->dhreadlast = Carbon::now();
            $saved = $leituraupdate->save();
            if (!$saved){
                throw new Exception('Falha ao salvar leitura!'); 
            }
            DB::commit();
            $ret->ok = true;
            $ret->msg = '' ;

        } catch (Exception $e) {
            DB::rollBack();
            $ret->ok = false;
            $ret->msg = $e->getMessage() ;
        }

        return $ret->toJson();

                
    }


}
