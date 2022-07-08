<?php

namespace App\Http\Controllers\API\v1\painelcliente;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\RetornoApiController;
use Illuminate\Support\Facades\DB;
use Exception;
use App\models\Contato;
use Carbon\Carbon;
use App\models\Clientes;
use App\models\ContatoCliente;
use App\models\ContatoPermissaoPainel;

class ContatosController extends Controller
{

  public function find(Request $request) {
    try{
      $ret = new RetornoApiController;
        $coluna = isset($request->coluna) ? $request->coluna : '';
        $search = isset($request->search) ? $request->search : '';
        $emailmd5 = isset($request->emailmd5) ? $request->emailmd5 : '';
        
        $condition = '=';
        if($coluna=='email'){
          $condition = 'like';
          $search = '%' . $search . '%';
        }
        
        $contato = Contato::select('id', 'nome', 'lixeira', 'email')
        ->where($coluna, $condition, $search)
        ->first();
        if(!$contato) throw new Exception("Nenhum contato encontrado.");
        if($contato->lixeira==1) throw new Exception("Contato inativado.");
        if($coluna=='email'){
          $validado = false;
          $emailslist = explode(';', $contato->email);
          foreach ($emailslist as $email) {
            $emailmd5check = md5('Contato-' . $email);
            if($emailmd5check == $emailmd5){
              $validado = true;
            }
          }
          if(!$validado) throw new Exception("E-mail não validado.");
        }

        $ret->ok = true;
        $ret->rows = $contato;
        
      } catch (Exception $e) {
        $ret->msg = $e->getMessage();
      }
      return $ret->toJson();
    }


    public function update(Request $request) {
        
        $ret = new RetornoApiController;
        try{
            $cnpjempresa = isset($request->cnpjempresa) ? $request->cnpjempresa : '';
            $cpfcnpj = isset($request->cpfcnpj) ? $request->cpfcnpj : '';
            $idrelacionamento = isset($request->idrelacionamento) ? intval($request->idrelacionamento) : 0;
            $nome = isset($request->nome) ? $request->nome : '';
            $login = strtoupper(isset($request->login) ? $request->login : '');
            $email = isset($request->email) ? $request->email : '';
            $ativo = isset($request->ativo) ? intval($request->ativo) : 0;
            $tel1 = isset($request->tel1) ? $request->tel1 : '';
            $tel2 = isset($request->tel2) ? $request->tel2 : '';
            $permissao = isset($request->permissao) ? $request->permissao : '';
            $permissoes  = explode(',', $permissao);

            $foto = '';
            $fotomd5 = '';
            if(isset($request->foto)){
                $foto = base64_decode($request->foto);
                $fotomd5 = isset($request->fotomd5) ? $request->fotomd5 : '';
            }
        
            
            if($login=='ADMIN'){
                $cpfcnpj = '22536307000144';
            }

            if($cnpjempresa=='')  throw new Exception("CNPJ da empresa inválido.");
            if($cpfcnpj=='')  throw new Exception("CPF/CNPJ do usuário inválido.");


            $empresacliente = Clientes::whereRaw('(cnpj=? or cpf=? or concat("000", ifnull(cpf,""))=?)', [$cnpjempresa, $cnpjempresa, $cnpjempresa])->first();
            if(!$empresacliente) throw new Exception('Empresa não foi localizada com o documento ' . $cnpjempresa);
            
            if($idrelacionamento>0){
                $contato = Contato::where('id', $idrelacionamento)->first();
            }else{
                $contato = Contato::where('cpfcnpj', $cpfcnpj)->first();
            }
            // if((!$contato) && ($login=='ADMIN')) throw new Exception('Contato ADMINISTRADOR i12 não foi localizado.');

            

           
        } catch (Exception $e) {
            $ret->msg = $e->getMessage();
            return $ret->toJson();
        }


        try{
            DB::beginTransaction();


            if(!($login=='ADMIN')){
                if(!$contato){
                    $contato = new Contato;
                }
                $contato->cpfcnpj  = $cpfcnpj;
                $contato->pessoa  = strlen($cpfcnpj)==14 ? 'J' : 'F';                
                $contato->nome  = $nome;
                $contato->cpfcnpj  = $cpfcnpj;
                $contato->email  = $email;
                $contato->tel1  = $tel1;
                $contato->tel2  = $tel2;
                $contato->lixeira  = $ativo == 1 ? 0 : 1;
                if($foto<>'') $contato->foto  = $foto;
                if($fotomd5<>'') $contato->fotomd5  = $fotomd5;
                $saved = $contato->save();
                if (!$saved)
                    throw new Exception('Falha ao salvar contato!'); 

                    $affectedRows = ContatoPermissaoPainel::where('idcontato', $contato->id)->where('idcliente', $empresacliente->codcliente)->delete();
                if($ativo){
                    foreach ($permissoes as $value) {
                        if (intval($value)>0){
                            $perm = new ContatoPermissaoPainel;
                            $perm->idpermissao = $value;
                            $perm->idcontato = $contato->id;
                            $perm->idcliente = $empresacliente->codcliente;
                            $saved = $perm->save();
                            if (!$saved){
                                throw new Exception('Falha ao salvar permissão!'); 
                            }                    
                        }
                    }
                }   
            }
            $relacao = ContatoCliente::where('idcontato', $contato->id)->where('idcliente', $empresacliente->codcliente)->first();
            if(!$relacao){
                $relacao = new ContatoCliente;
                $relacao->idcontato = $contato->id;
                $relacao->idcliente  = $empresacliente->codcliente;
            }
            $relacao->ultimosynci12  = Carbon::now();               
            $saved = $relacao->save();
            if (!$saved)
                throw new Exception('Falha ao salvar relacionamento!'); 
        

            DB::commit();
            $ret->id = $contato->id;
            $ret->ok = true;
        } catch (Exception $e) {
            DB::rollBack();
            $ret->msg = $e->getMessage();
        }           
        return $ret->toJson();
    }



    public function changepassword_external(Request $request) {
        
        $ret = new RetornoApiController;
        try{
            $cpfcnpj = isset($request->cpfcnpj) ? $request->cpfcnpj : '';
            $pwdcrypt = isset($request->pwdcrypt) ? $request->pwdcrypt : '';
            if($cpfcnpj == '22536307000144') throw new Exception("Super usuário ADMIN protegido.");
            $contato = Contato::where('cpfcnpj', $cpfcnpj)->first();
            if(!$contato) throw new Exception('Contato não foi encontrado.');
            if($contato->lixeira==1) throw new Exception('Contato não foi desativado.');
        } catch (Exception $e) {
            $ret->msg = $e->getMessage();
            return $ret->toJson();
        }


        try{
            DB::beginTransaction();

            $contato->password  = $pwdcrypt;
            $saved = $contato->save();
            if (!$contato) throw new Exception('Falha ao alterar senha!'); 
           
            DB::commit();
            $ret->ok = true;
        } catch (Exception $e) {
            DB::rollBack();
            $ret->msg = $e->getMessage();
        }           
        return $ret->toJson();
    }    
}
