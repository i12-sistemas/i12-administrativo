<?php

namespace App\Http\Controllers\API\v1\icomservices\painelcliente;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\RetApiController;
use Exception;
use Illuminate\Support\Facades\DB;
use Validator;
use App\models\Contato;
use App\models\ContatoCliente;
use App\models\OrdemServico;
use Carbon\Carbon;
use Illuminate\Support\Str;
          
class AtendimentoController extends Controller
{ 
  public function contadores(Request $request) {
    $ret = new RetApiController;
    try{
      $contato = session('contato');
      if(!$contato) throw new Exception("Contato não encontrado");
      $cliente = session('cliente');
      if(!$cliente) throw new Exception("Cliente não encontrado");


      $emabertos = OrdemServico::where('osordem.idclientecontato', $contato->id)
                      ->where('osordem.idcliente', $cliente->codcliente)
                      ->where('osordem.modo', 'OS')
                      ->where('osordem.situacao', 'Aberta')
                      ->count();

      $pendenteresposta = OrdemServico::where('osordem.idclientecontato', $contato->id)
                        ->where('osordem.idcliente', $cliente->codcliente)
                        ->where('osordem.modo', '=', 'OS')
                        ->where('osordem.situacao', '=', 'Aberta')
                        ->where('osordem.pendentecliente', '=', 1)
                        ->orderBy('osordem.dtabertura', 'desc')
                        ->count();


      $naolidos = OrdemServico::where('osordem.idclientecontato', $contato->id)
                      ->where('osordem.idcliente', $cliente->codcliente)
                      ->where('osordem.modo', 'OS')
                      ->whereNull('osordem.contato_leitura_at')
                      ->whereRaw('if(osordem.situacao="Aberta", true, datediff(now(), dtFechamento)<=15 )')
                      ->count();

        $dados = [
          'emabertos' => $emabertos,
          'pendenteresposta' => $pendenteresposta,
          'naolidos'  => $naolidos
        ];


        $ret->ok = true;
        $ret->data = $dados;

    } catch (Exception $e) {
        $ret->msg = $e->getMessage();
    }

    return $ret->toJson();
  }   

  public function contatoupdate(Request $request) {
    $ret = new RetApiController;
    try{
      $cliente = session('cliente');
      if(!$cliente) throw new Exception("Cliente não encontrado");
 

      $rules = [
        'email' => ['email'],
        'nome' => ['min:2', 'max:255'],
        'tel1' => ['max:25'],
        'tel2' => ['max:25'],
      ];
      $messages = [
          'size' => 'O campo :attribute, deverá ter :max caracteres.',
          'integer' => 'O conteudo do campo :attribute deverá ser um número inteiro.',
          'unique' => 'O conteudo do campo :attribute já foi cadastrado.',
          'required' => 'O conteudo do campo :attribute é obrigatório.',
      ];

      $validator = Validator::make($request->all(), $rules, $messages);
      if ($validator->fails()) {
          $msgs = [];
          $errors = $validator->errors();
          foreach ($errors->all() as $message) {
              $msgs[] = $message;
          }
          $ret->data = $msgs;
          throw new Exception(join("; ", $msgs));
      }

      

      $inativar = $request->has('inativar') ? boolval($request->inativar) : false;

      $contatoemail = null;
      $email = null;
      if ($request->has('email')){
        $email = strval($request->email);
        if (!isemail($email)) throw new Exception('E-mail inválido');
        $emailchecked_at = $request->has('emailchecked_at') ? $request->emailchecked_at : null;
        if (!$emailchecked_at) throw new Exception('Data de validação do e-mail inválida');
        try {
          $emailchecked_at = Carbon::createFromFormat('Y-m-d H:i:s', $emailchecked_at, 'America/Sao_Paulo');
        } catch (\Throwable $th) {
          new Exception('Data de validação do e-mail inválida - ' . $th->getMessage());
        }
        $emailcheckeduuid = $request->has('emailcheckeduuid') ? $request->emailcheckeduuid : null;
        if ($emailcheckeduuid ? $emailcheckeduuid === '' : true) throw new Exception('Uuid de validação do e-mail inválida');

        $contatoemail = Contato::where('emailprincipal', '=', $email)->withTrashed()->first();
      }

      $contatowhatsapp = null;
      $whatsapp = null;
      if ($request->has('whatsapp')){
        $whatsapp = strval($request->whatsapp);
        if ($whatsapp ? $whatsapp === '' : true) throw new Exception('WhatsApp inválido');
        $whatsappchecked_at = $request->has('whatsappchecked_at') ? $request->whatsappchecked_at : null;
        if (!$whatsappchecked_at) throw new Exception('Data de validação do WhatsApp inválida');
        try {
          $whatsappchecked_at = Carbon::createFromFormat('Y-m-d H:i:s', $whatsappchecked_at, 'America/Sao_Paulo');
        } catch (\Throwable $th) {
          new Exception('Data de validação do WhatsApp inválida - ' . $th->getMessage());
        }
        $whatsappcheckeduuid = $request->has('whatsappcheckeduuid') ? $request->whatsappcheckeduuid : null;
        if ($whatsappcheckeduuid ? $whatsappcheckeduuid === '' : true) throw new Exception('Uuid de validação do WhatsApp inválida');

        $contatowhatsapp = Contato::where('whatsapp', '=', $whatsapp)->withTrashed()->first();
        
      }    

      $contatorelacionamento = null;
      $relacionamentoid = null;
      if ($request->has('relacionamentoid')){
        $relacionamentoid = intval($request->relacionamentoid);
        if ($relacionamentoid ? !($relacionamentoid > 0) : true) throw new Exception('Id de relacionamento inválido');
        $contatorelacionamento = Contato::where('id', '=', $relacionamentoid)->withTrashed()->first();
        if (!$contatorelacionamento) throw new Exception('Nenhum usuário encontrado com o Id de relacionamento informa');


        $clientecount = ContatoCliente::where('idcontato', $contatorelacionamento->id)->count();

        if ($contatorelacionamento->emailvalidado && $email && ($clientecount > 1) && !$inativar) 
          throw new Exception('Não é permitido atualizar o e-mail para usuário com mais de um cliente associado');
        
          if ($contatorelacionamento->whatsappvalidado && $whatsapp && ($clientecount > 1) && !$inativar) 
          throw new Exception('Não é permitido atualizar o WhatsApp para usuário com mais de um cliente associado');
      }    

      if ((!$email) && (!$whatsapp)) throw new Exception('Nenhuma chave (e-mail ou WhatsApp) informados');

      // se existe os dois porém são diferentes dará erro
      if ($contatoemail && $contatowhatsapp) {
        if ($contatoemail->id !== $contatowhatsapp->id) 
        throw new Exception('Suas chaves (e-mail ou WhatsApp) estão sendo usados em contatos diferentes. Informe uma chave sem duplicidade');
      }
      
      // se existe os dois porém são diferentes dará erro
      if ($contatorelacionamento) {
        if ($contatowhatsapp ? ($contatorelacionamento->id !== $contatowhatsapp->id) : false)
        throw new Exception('Sua chave de WhatsApp está sendo usada em contato diferente do Id de relacionamento atual. Informe uma chave sem duplicidade');
        
        if ($contatoemail ? ($contatorelacionamento->id !== $contatoemail->id) : false)
        throw new Exception('Sua chave de e-mail está sendo usada em contato diferente do Id de relacionamento atual. Informe uma chave sem duplicidade');
      }

      $action = null;
      $contatoUpdate = null;
      if ($contatorelacionamento ? $contatorelacionamento->id > 0 : false) {
        $action = 'update';
        $contatoUpdate = $contatorelacionamento;
      } else {
        $action = 'insert';
        if ($contatoemail ? $contatoemail->id > 0 : false) {
          $action = 'update';
          $contatoUpdate = $contatoemail;
        }
        if (($contatowhatsapp ? $contatowhatsapp->id > 0 : false) && ($action === 'insert')) {
          $action = 'update';
          $contatoUpdate = $contatowhatsapp;
        }
      }


      try{
        DB::beginTransaction();

        if ($action === 'insert') {
          $contatoUpdate = new Contato;
          $contatoUpdate->lixeira = 0;
          $contatoUpdate->deleted_at = null;
          $contatoUpdate->permissoes = 'operador';
        } else {
          if ($inativar) {
            ContatoCliente::where('idcontato', $contatoUpdate->id)->where('idcliente', $cliente->codcliente)->delete();

            $count = ContatoCliente::where('idcontato', $contatoUpdate->id)->count();
            if ($count ? $count === 0 : true) {
              $contatoUpdate->lixeira = 1;
              $contatoUpdate->deleted_at = Carbon::now();
            }
          } else {
            $contatoUpdate->lixeira = 0;
            $contatoUpdate->deleted_at = null;
          }          
        }

        if ($request->has('nome')) $contatoUpdate->nome = $request->nome;
        // if ($request->has('cpfcnpj')) $contatoUpdate->cpfcnpj = cleancnpjcpf($request->cpfcnpj);
        // $contatoUpdate->pessoa = Str::length($contatoUpdate->cpfcnpj) === 14 ? 'J' : 'F';
        if ($request->has('tel1')) $contatoUpdate->tel1 = cleancnpjcpf($request->tel1);
        if ($request->has('tel2')) $contatoUpdate->tel2 = cleancnpjcpf($request->tel2);

        if ($request->has('foto') && $request->has('fotomd5ext')) {
          $foto = base64_decode($request->foto);
          $contatoUpdate->foto  = $foto;
          $contatoUpdate->fotomd5  = $request->fotomd5ext;
        }

        $contatoUpdate->i12ultimaatualizacao = Carbon::now();

        if ($request->has('email') && !$inativar) {
          $contatoUpdate->emailprincipal = $email;
          $contatoUpdate->emailchecked_at = $emailchecked_at;
          $contatoUpdate->emailcheckeduuid = $emailcheckeduuid;
        }

        if ($request->has('whatsapp') && !$inativar) {
          $contatoUpdate->whatsapp = $whatsapp;
          $contatoUpdate->whatsappchecked_at = $whatsappchecked_at;
          $contatoUpdate->whatsappcheckeduuid = $whatsappcheckeduuid;
        }
        $contatoUpdate->save();
        
        if (!$inativar) {
          $relacao = ContatoCliente::where('idcontato', $contatoUpdate->id)->where('idcliente', $cliente->codcliente)->first();
          if(!$relacao){
            $relacao = new ContatoCliente;
            $relacao->idcontato = $contatoUpdate->id;
            $relacao->idcliente  = $cliente->codcliente;
            $relacao->permissoes  = 'operador';
          }
          $relacao->ultimosynci12  = Carbon::now();               
          $relacao->save();
        }
        
        DB::commit();
      } catch (Exception $e) {
          DB::rollBack();
          throw new Exception($e->getMessage());
      }         

      $dados = [
        'action' => $action,
        'id' => $contatoUpdate->id,
        'nome' => $contatoUpdate->nome
      ];
      if ($contatoUpdate->emailvalidado) {
        $dados['email'] = $contatoUpdate->emailprincipal;
        $dados['emailchecked_at'] = $contatoUpdate->emailchecked_at ? $contatoUpdate->emailchecked_at->format('Y-m:d H:i:s') : null;
        $dados['emailcheckeduuid'] = $contatoUpdate->emailcheckeduuid;
      }
      if ($contatoUpdate->whatsappvalidado) {
        $dados['whatsapp'] = $contatoUpdate->whatsapp;
        $dados['whatsappchecked_at'] = $contatoUpdate->whatsappchecked_at ? $contatoUpdate->whatsappchecked_at->format('Y-m:d H:i:s') : null;
        $dados['whatsappcheckeduuid'] = $contatoUpdate->whatsappcheckeduuid;
      }      
      $dados['empresa'] = [
        'razaosocial' => $cliente->nome,
        'doc' => $cliente->doc,
      ];
      \Log::debug($contatoUpdate);
      \Log::debug($dados);
      $ret->id =  $contatoUpdate->id;
      $ret->ok = true;
      $ret->data = $dados;

    } catch (Exception $e) {
        $ret->msg = $e->getMessage();
    }

    return $ret->toJson();
  }   

  public function url(Request $request)
  {
    $ret = new RetApiController;
    try {
      $url = '';
      $tag = $request->has('tag') ?$request->tag : '';
      switch ($tag) {
        case 'paineldocliente':
          $url = env('APP_URL_CLIENTE', '');
          # code...
          break;
        case 'basedeconhecimento':
          $url = 'https://ajuda.i12.com.br';
          # code...
          break;
        case 'chat':
          $url = 'https://api.whatsapp.com/send?phone=5516999590260';
          # code...
          break;
        
        default:
          # code...
          break;
      }
      $ret->data = $url;
      $ret->ok = ($url !== '');
    } catch (\Throwable $th) {
      $ret->msg = $th->getMessage();
    }
    return $ret->toJson();
  }  

  
  public function enviaNotificacaoAtendimento(Request $request, $numero) {
    $ret = new RetApiController;
    try {
      $atendimento = OrdemServico::find($numero); 
      if (!$atendimento) throw new Exception("Nenhum atendimento encontrado com o número " . $numero);
      
      $rawreturn = $request->has('rawreturn') ? boolval($request->rawreturn) : false;
      $interacoes = $atendimento->interacoespublicas()->where('interacao.sendmail', '=', 1)->where('emailstatus', '=', 0)->get();
      if(!$interacoes) throw new Exception("Nenhum interação pendente envio de notificação");
      

      try {
        $contato = $atendimento->contato;
        if (!$contato) throw new Exception("Nenhum contato associado ao atendimento");
        if (!$contato->emailvalidado && !$contato->whatsappvalidado) throw new Exception("Contato não tem nenhuma método de comunicação validado");

        $canais = [];
        if ($contato->emailvalidado) $canais[] = $contato->emailprincipal;
        if ($contato->whatsappvalidado) $canais[] = $contato->whatsapp;
        
        $ret->ok = true;
        $ret->msg = 'Enviado para ' . $contato->nome . ' em ' . implode(', ', $canais);
      } catch (\Throwable $th) {
        // registra erro
        try{
          DB::beginTransaction();

          foreach ($interacoes as $interacao) {
            $interacao->emailstatus = 2;
            $interacao->emailerror = $e->getMessage();
            $interacao->emaildhprocessado = Carbon::now();
            $interacao->save();
          }

          DB::commit();

        } catch (Exception $e) {
          DB::rollBack();
          throw new Exception($e->getMessage());
        }   
        throw new Exception($e->getMessage());
      }


      // // contato
      // $contato = null;
      // // se for do operador vai para o cliente
      // if($interacao->origem=='O'){
      //     if($chamado->contato){
      //         $emailslist = explode(';', $chamado->contato->email);
      //         $emails = [];
      //         foreach ($emailslist as $email) {
      //             $emails[] = (object)['email' => $email, 
      //                         'urlpublico' => url('/painelcliente/') . '/chamado/show/publico/' . 
      //                                             base64_encode($chamado->id) . '/' . 
      //                                             md5($chamado->id . $chamado->dtabertura->format('Y-m-d H:i:s') . "OS") . 
      //                                             '/' . base64_encode($email) . 
      //                                             '/' . md5('Contato-' . $email) 
      //                     ];
      //         }
      //         $contato = (object)[
      //                     'nome' => $chamado->contato->nome,
      //                     'emails' => $emails
      //                 ];
      //     }      
      // }else{
      //     if($chamado->usuario){
      //         $emails[] = (object)['email' => $chamado->usuario->email, 
      //                             'urlpublico' => ''
      //                     ];
              
      //         $contato = (object)[
      //                     'nome' => $chamado->usuario->nome,
      //                     'emails' => $emails
      //                 ];
      //     }

      // }
      // if($contato){
      //     foreach ($contato->emails as $email) {
              
      //         if($email->email<>''){
      //             $destinatario = (object)[   'nome' => $contato->nome,
      //                                         'email' => $email->email,
      //                                         'link' => $email->urlpublico
      //                                     ];
                
      //             $job = new ChamadosJob($chamado, $interacao, $destinatario);
      //             dispatch($job);
      //         }
      //     }
      // }  
      
    } catch (Exception $e) {
      $ret->msg = $e->getMessage();
    }                      

    return $rawreturn ? $ret : $ret->toJson();
  }   

}
