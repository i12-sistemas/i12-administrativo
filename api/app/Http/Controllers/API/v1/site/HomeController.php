<?php

namespace App\Http\Controllers\API\v1\site;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\models\Contatosite;
use App\Http\Controllers\RetornoApiController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;
use App\models\Empresa;
use App\models\Configuracao;
use App\Jobs\SiteContato;


class HomeController extends Controller
{
    
    public function contatoadd(Request $request)
    {   
        $ret = new RetornoApiController;
        try{


            $rules = [
                'nome'          => 'required|between:3,255',
                'email'         => 'required|between:3,255',
                'telefone'      => 'required|between:4,25',
                'assunto'       => 'required|between:3,255',
                'mensagem'      => 'required'
            ];
            $messages = [
                'required'    => 'Campo :attribute é obrigatório.',
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
            
            $nome = ucutf8dec(trim(isset($request->nome) ? $request->nome : ''));
            $email = ucutf8dec(trim(isset($request->email) ? $request->email : ''));
            $telefone = ucutf8dec(trim(isset($request->telefone) ? $request->telefone : ''));
            $assunto = ucutf8dec(trim(isset($request->assunto) ? $request->assunto : ''));
            $mensagem = ucutf8dec(trim(isset($request->mensagem) ? $request->mensagem : ''));
           
            $destinatarios = [];
            $emails = [];
            $config = Configuracao::select('texto')->find('SITE_EMAILS_CONTATO');
           
            if($config){
                $destinterno = json_decode($config->texto);
                if(count($destinterno)==0){
                    throw new Exception('Nenhum email de administração cadastrado no sistema. Faça contato por telefone!');
                }
                foreach($destinterno as $destinatario){
                    $destinatarios[] = (object)$destinatario;
                    $emails[] = $destinatario->email;
                }
            }else{
                throw new Exception('Nenhum email de administração cadastrado no sistema. Faça contato por telefone!');
            }
            $listdeststr = implode(',', $emails);
            

            
        } catch (Exception $e) {
            $ret->ok = false;
            $ret->msg = $e->getMessage() ;
            return $ret->toJson();
        }     
        
        try{
            DB::beginTransaction();

            $msg  = new Contatosite;
            $msg->ip = \Request::getClientIp(true);
            $msg->dh = Carbon::now();
            $msg->nome = $nome;
            $msg->email = $email;
            $msg->telefone = $telefone;
            $msg->assunto = $assunto;
            $msg->mensagem = $mensagem;
            $msg->destinatario = $listdeststr;
            $upd = $msg->save();
            if (!$upd){
                throw new Exception('Mensagem não foi envida!'); 
            }

            $ret->id = $msg->id;
            DB::commit();
            $ret->ok = true;
        } catch (Exception $e) {
            DB::rollBack();
            $ret->ok = false;
            $ret->msg = $e->getMessage() ;
        }   
        


        //send mail
        try {
            $empresa = Empresa::first();
            
            $destinatarios[] = (object)['nome' => $nome,'email' => $email, 'tipo' => 'user'];
        
            foreach($destinatarios as $destinatario){
                $job = new SiteContato($nome, $telefone, $email, $assunto, $mensagem, $destinatario, $empresa);
                dispatch($job);            
            }
            
        } catch (Exception $e) {
            return $ret->msg = $e->getMessage();
        }        
          
        return $ret->toJson();             
        
    }  
}
