<?php

namespace App\Http\Controllers\api\v1\icomServices;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Controllers\RetornoApiController;
use Exception;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use App\models\i12ChecagemMeioComunicacao;
use App\Http\Controllers\RetApiController;
use App\Http\Controllers\TopZapiChatController;
use Validator;
use App\Jobs\icomServices\ValidacaoMeioComunicacaoJob;
use Jenssegers\Agent\Facades\Agent;

class ValidarMeioComunicacaoController extends Controller
{

  
  public function verificar(Request $request)  {
    $ret = new RetApiController;
    try {
      $empresa= session('empresa');
      if (!$empresa) throw new Exception('Nenhuma empresa informada');

      $rules = [
        'email' => ['email'],
        'whatsapp' => ['string', 'min:8', 'max:13'],
        'from' => ['string', 'min:1', 'max:255', 'required'],
        'subject' => ['string', 'min:1', 'max:255', 'required'],
      ];
      $messages = [
          'email' => 'E-mail inválido',
          'size' => 'O campo :attribute, deverá ter :max caracteres.',
          'integer' => 'O conteudo do campo :attribute deverá ser um número inteiro.',
          'unique' => 'O conteudo do campo :attribute já foi cadastrado.',
          'required' => 'O conteudo do campo :attribute é obrigatório.',
          'max' => 'O conteudo do campo :attribute é deve ser menor do que :max caracteres',
          'min' => 'O conteudo do campo :attribute é deve ser maior do que :min caracteres',
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
      
      if ($request->has('email') && $request->has('whatsapp')) 
        throw new Exception('Informe somente um método de validação E-mail ou WhatsApp');

   
      $chave = $request->has('email') ? $request->email : $request->whatsapp;
      $force = $request->has('force') ? boolval($request->force) : false;
      $sendlink = $request->has('sendlink') ? boolval($request->sendlink) : false;

      
      if (!$force) {
        $novo = i12ChecagemMeioComunicacao::where('chave', '=', $chave)
                        ->whereNotNull('checked_at')
                        ->first();
        if ($novo) {
          $data = [
            'checked_at' => $novo->checked_at->format('Y-m-d H:i:s'),
          ];
          if (($novo->origem ?$novo->origem !== '' : false) || ($novo->origemid ? $novo->origemid > 0 : false)) {
            $data['origem'] = $novo->origem;
            $data['origemid'] = $novo->origemid;
          }
          $ret->data = $data;
          $ret->id = $novo->id;
          $ret->ok=True;          
          throw new Exception($novo->chave . '  verificada anteriormente.');
        }
      }
     
      try{
        DB::beginTransaction();

        i12ChecagemMeioComunicacao::where('chave', '=', $chave)
                                ->where('cnpj', '=', $empresa->cnpj)
                                ->where('created_at', '<=', Carbon::now())
                                ->whereNull('checked_at')
                                ->whereNull('deleted_at')
                                ->delete();

        $novo = new i12ChecagemMeioComunicacao();
        $novo->tipo = $request->has('email') ? 'email' : 'whatsapp';
        $novo->created_at = Carbon::now();
        $novo->expire_at = Carbon::now()->addHours($request->has('email') ? 24 : 6);
        $novo->chave = $chave;
        $novo->cnpj = $empresa->cnpj;

        if ($request->has('sendlink')) $novo->sendlink = $sendlink;
        if ($request->has('from')) $novo->from = $request->from;
        if ($request->has('subject')) $novo->subject = $request->subject;
        if ($request->has('message')) $novo->message = $request->message;
        if ($request->has('origem')) $novo->origem = $request->origem;
        if ($request->has('origemid')) $novo->origemid = $request->origemid;

        $code = rand(100000, 999999);
        $novo->code = $code; 

        $novo->save();

        DB::commit();
      } catch (Exception $e) {
          DB::rollBack();
          throw new Exception($e->getMessage());
      }    
      
      
      if (($novo->origem ?$novo->origem !== '' : false) || ($novo->origemid ? $novo->origemid > 0 : false)) {
        $ret->data = [
          'origem' => $novo->origem,
          'origemid' => $novo->origemid,
        ];
      }

      $ret->id = $novo->id;
      $ret->ok=True;

      $job = new ValidacaoMeioComunicacaoJob($novo);
      dispatch($job);

    } catch (\Throwable $th) {
      $ret->msg = $th->getMessage();
      return $ret->toJson();
    }
    return $ret->toJson();

  }  

  
  public function validar(Request $request)  {
    $ret = new RetApiController;
    try {
      $sendlink = $request->has('sendlink') ? boolval($request->sendlink) : false;

      $rules = [
        'email' => ['email'],
        'whatsapp' => ['string', 'min:8', 'max:13'],
        'token' => ['string', 'min:1', 'max:255', 'required'],
        // 'code' => ['string', 'min:1', 'max:255', 'required'],
        
      ];
      if ($sendlink) $rules[] = [ recaptchaFieldName() => recaptchaRuleName() ];
      $messages = [
          'email' => 'E-mail inválido',
          'size' => 'O campo :attribute, deverá ter :max caracteres.',
          'integer' => 'O conteudo do campo :attribute deverá ser um número inteiro.',
          'unique' => 'O conteudo do campo :attribute já foi cadastrado.',
          'required' => 'O conteudo do campo :attribute é obrigatório.',
          'max' => 'O conteudo do campo :attribute é deve ser menor do que :max caracteres',
          'min' => 'O conteudo do campo :attribute é deve ser maior do que :min caracteres',
          recaptchaFieldName() => 'Validação humana falhou',
          'recaptcha' => 'Validação humana falhou',
          'validation.recaptcha' => 'Validação humana falhou',
          'g-recaptcha-response' => 'Validação humana falhou',
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
      
      
      $code = $request->has('code') ? trim($request->code) : null;
      if ($code === '') $code = null;
      if ((!$sendlink) && ($code ? $code === '' : true)) 
        throw new Exception('Nenhum código informado');      


      if ($request->has('email') && $request->has('whatsapp')) 
        throw new Exception('Informe somente um método de validação E-mail ou WhatsApp');

      $chave = $request->has('email') ? $request->email : $request->whatsapp;
      $check = i12ChecagemMeioComunicacao::find( $request->token);
      if (!$check) throw new Exception('Nenhum registro localizado');
      
      if ($check->code !== $request->code) throw new Exception('Código inválido para o token');
      if ($check->chave !== $chave) throw new Exception('Chave inválida para o token');
      if ($check->expirado) throw new Exception('Token expirado');
      if ($check->checked_at) {
        $data = [
          'checked_at' => $check->checked_at->format('Y-m-d H:i:s'),
        ];
        if (($check->origem ? $check->origem !== '' : false) || ($check->origemid ? $check->origemid > 0 : false)) {
          $data['origem'] = $check->origem;
          $data['origemid'] = $check->origemid;
        }
        $ret->data = $data;
        $ret->id = $check->id;
        $ret->ok=True;        
        throw new Exception('Token verificado anteriormente');
      }

      
      try{
        DB::beginTransaction();

        $check->checked_at = Carbon::now();
        $check->ip = \Request::getClientIp(true);
        $check->dispositivo = $_SERVER['HTTP_USER_AGENT'];
        $check->so = '';
        $check->save();

        DB::commit();
      } catch (Exception $e) {
          DB::rollBack();
          throw new Exception($e->getMessage());
      }    
      
      
      $data = [
        'checked_at' => $check->checked_at->format('Y-m-d H:i:s'),
      ];
      if (($check->origem ? $check->origem !== '' : false) || ($check->origemid ? $check->origemid > 0 : false)) {
        $data['origem'] = $check->origem;
        $data['origemid'] = $check->origemid;
      }
      $ret->data = $data;
      $ret->id = $check->id;
      $ret->msg='Chave verificada com sucesso!';
      $ret->ok=True;

      // $job = new ValidacaoMeioComunicacaoJob($novo);
      // dispatch($job);

    } catch (\Throwable $th) {
      $ret->msg = $th->getMessage();
      return $ret->toJson();
    }
    return $ret->toJson();
  }

  public function zap(Request $request)  {
    $ret = new RetApiController;
    try {
      // $empresa= session('empresa');
      // if (!$empresa) throw new Exception('Nenhuma empresa informada');

      $rules = [
        'whatsapp' => ['string', 'min:8', 'max:13'],
        'msg' => ['string', 'min:1', 'max:255', 'required'],
      ];
      $messages = [
          'email' => 'E-mail inválido',
          'size' => 'O campo :attribute, deverá ter :max caracteres.',
          'integer' => 'O conteudo do campo :attribute deverá ser um número inteiro.',
          'unique' => 'O conteudo do campo :attribute já foi cadastrado.',
          'required' => 'O conteudo do campo :attribute é obrigatório.',
          'max' => 'O conteudo do campo :attribute é deve ser menor do que :max caracteres',
          'min' => 'O conteudo do campo :attribute é deve ser maior do que :min caracteres',
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

      $zap = new TopZapiChatController();
      $zap->sendText($request->whatsapp, $request->msg);
      dd('ok');
      $ret->ok=True;
    } catch (\Throwable $th) {
      $ret->msg = $th->getMessage();
      return $ret->toJson();
    }
    return $ret->toJson();
  }
}
