<?php

namespace App\Http\Controllers\api\v1\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Exception;
use Carbon\Carbon;
use App\Http\Controllers\RetApiController;

use Validator;
use App\models\ContatoCliente;
use App\models\Contato;
use App\models\ContatoTokens;
use App\models\ContatoResetPwdTokens;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

use App\Jobs\auth\ContatoResetPwdRequestJob;


class ContatoAuthController extends Controller
{
  
  // autenticação
  public function auth(Request $request)
  {
    $ret = new RetApiController;
    // autenticação de usuário e senha chega no header
    try {
      header('Cache-Control: no-cache, must-revalidate, max-age=0');
      $has_supplied_credentials = !(empty($_SERVER['PHP_AUTH_USER']) && empty($_SERVER['PHP_AUTH_PW']));
      if (!$has_supplied_credentials) 
        throw new Exception('Credenciais inválidas');

      $captcha = env('RECAPTCHA_SITE_KEY', '') !== '';
      if ($captcha) {
        $rules = [
          recaptchaFieldName() => recaptchaRuleName()
        ];
        $messages = [
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
      }

      $AUTH_USER = $_SERVER['PHP_AUTH_USER'];
      $AUTH_PASS = $_SERVER['PHP_AUTH_PW'];

      $field = isemail($AUTH_USER) ? 'emailprincipal' : 'whatsapp';

      $contato = Contato::where($field, '=', $AUTH_USER)->first();
      if (!$contato) 
        throw new Exception('Nenhum usuário encontrado');

    
      if (!\Hash::check($AUTH_PASS , $contato->password))
          throw new Exception('Usuário e senha inválidos');        

      $clientes = $contato->clientes;
      if (!$clientes) throw new Exception('Nenhuma empresa associada ao seu usuário');
      if (count($clientes) <= 0) throw new Exception('Nenhuma empresa associada ao seu usuário');

      $listacliente = [];
      foreach ($clientes as $row) {
        $listacliente[] = [
          'id' => $row->codcliente,
          'doc' => $row->doc,
          'nome' => $row->nome,
        ];
      }


      if (count($listacliente) === 1) {
        $cliente = $listacliente[0];
      } else {
        $cliente = null;
      }

    } catch (\Throwable $th) {
      $ret->msg = $th->getMessage();
      return $ret->toJson();
    }


    try {
      session_start();

      // if ($forcenewsession) 
      session_regenerate_id();
      $sessionid = session_id();

      DB::connection('mysql')->beginTransaction();

      $newsession = ContatoTokens::find($sessionid);
      if (!$newsession) {
        $newsession = new ContatoTokens;
        $newsession->session = session_id();
        $newsession->contatoid = $contato->id;
        $newsession->username = $AUTH_USER;
        if ($cliente) {
          $newsession->clienteid = $cliente['id'];
          $newsession->gerarAccessCode();
        }
        $newsession->expire_at = Carbon::now()->addHour(24);
        $newsession->token = \Hash::make($AUTH_USER . $contato->password . $contato->id, ['rounds' => 12]);
        $newsession->ip = \Request::getClientIp(true);
        $newsession->save();
      }

      DB::connection('mysql')->commit();

      $dados = [  
        'username' => $AUTH_USER,
        'token' => $newsession->token,
        'expire_at' => $newsession->expire_at->format('Y-m-d H:i:s'),
      ];

      if ($newsession->accesscode ? $newsession->accesscode !== '' : false ) {
        $dados['accesscode'] = $newsession->accesscode;
        $dados['cliente'] = $cliente;
      } else {
        $dados['clientes'] = $listacliente;
      }

      $ret->data = $dados;
      $ret->ok = true;

    } catch (\Throwable $th) {
      DB::connection('mysql')->rollBack();
      $ret->msg = $th->getMessage();
    }
    return $ret->toJson();
  }

  public function auth2(Request $request)
  {
    $ret = new RetApiController;
    // autenticação de usuário e senha chega no header
    try {
      $rules = [
        'username' => ['string', 'min:5', 'required'],
        'token' => ['string', 'min:20', 'required'],
        'clientedoc' => ['string', 'min:11', 'max:14', 'required'],
        'clienteid' => ['integer', 'required'],
      ];
      $messages = [
          'size' => 'O campo :attribute, deverá ter :max caracteres.',
          'min' => 'O campo :attribute, deverá ter no mínimo :min caracteres.',
          'max' => 'O campo :attribute, deverá ter no máximo :max caracteres.',
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

      $token = ContatoTokens::where('token', '=', $request->token)->first();
      if (!$token) throw new Exception('Token inválido');
      if ($token->accesscode ? $token->accesscode !== '' : false) throw new Exception('Token já foi usado');
      if (!$token->contato) throw new Exception('Nenhum contato associado ao token');
      
      
      $cliente = $token->contato->clientes()->where('codcliente', '=', $request->clienteid)->first();
      if (!$cliente) throw new Exception('Nenhum cliente encontrado');
      if ($cliente->ativo !== 1) throw new Exception('Cliente inativo');
      if ($cliente->doc !== $request->clientedoc) throw new Exception('Documento do cliente não confere');
      
      $cliente = [
        'id' => $cliente->codcliente,
        'doc' => $cliente->doc,
        'nome' => $cliente->nome
      ];

    } catch (\Throwable $th) {
      $ret->msg = $th->getMessage();
      return $ret->toJson();
    }


    try {

      DB::connection('mysql')->beginTransaction();

      $token->clienteid = $cliente['id'];
      $token->gerarAccessCode();
      $token->save();

      DB::connection('mysql')->commit();

      $dados = [  
        'username' => $token->username,
        'token' => $token->token,
        'expire_at' => $token->expire_at->format('Y-m-d H:i:s'),
        'accesscode' => $token->accesscode,
        'cliente' => $cliente,
      ];

      $ret->data = $dados;
      $ret->ok = true;

    } catch (\Throwable $th) {
      DB::connection('mysql')->rollBack();
      $ret->msg = $th->getMessage();
    }
    return $ret->toJson();
  }

  public function checklogin(Request $request)
  {
    $ret = new RetApiController;
    // autenticação de usuário
    try {
      header('Cache-Control: no-cache, must-revalidate, max-age=0');

      $has_supplied_credentials = !(empty($_SERVER['PHP_AUTH_USER']) && empty($_SERVER['PHP_AUTH_PW']));
      if (!$has_supplied_credentials) 
        throw new Exception('Acesso não autorizado!');

      $token = trim($_SERVER['PHP_AUTH_USER']);
      if ($token == '') throw new Exception('Token inválido');

      $accesscode = $_SERVER['PHP_AUTH_PW'];
      if ($accesscode == '') throw new Exception('AccessCode inválido');

      $session = ContatoTokens::where('token', $token)->where('accesscode', '=', $accesscode)->first();
      if (!$session) throw new Exception('Sessão inválida');
      if ($session->expire_at < Carbon::now()) throw new Exception('Sessão expirada. Refaça seu login.');
      if (!$session->contato) throw new Exception('Sessão incompleta. Refaça seu login.');
      if (!$session->cliente) throw new Exception('Sessão incompleta. Refaça seu login.');
      $cliente = $session->cliente;

      $contatolink = ContatoCliente::where('idcontato', $session->contato->id)->where('idcliente', $cliente->codcliente)->first();
      $permissoes = null;
      if ($contatolink ? $contatolink->permissoes !== '' : false) $permissoes = $contatolink->permissoeslista;
      $contatomerge = $session->contato->exportLogin();
      $contatomerge['permissoes'] = $permissoes;


      $data = [
        'contato' => $contatomerge,
        'cliente' => [
          'id' => $cliente->codcliente,
          'doc' => $cliente->doc,
          'nome' => $cliente->nome
        ]
      ];
      $ret->data = $data;
      $ret->ok = true;

    } catch (\Throwable $th) {
      $ret->msg = $th->getMessage();
      return $ret->toJson();
    }
    return $ret->toJson();
  }

  public function resetpwd_request(Request $request)
  {

    $ret = new RetApiController;
    try {
      $username = $request->has('username') ? $request->username : '';
      if($username ? $username === '' : true)
        throw new Exception('Usuário não informado');

      $captcha = env('RECAPTCHA_SITE_KEY', '') !== '';
      if ($captcha) {
        $rules = [
          recaptchaFieldName() => recaptchaRuleName()
        ];
        $messages = [
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
      }
  
      $field = isemail($username) ? 'emailprincipal' : 'whatsapp';

      $contato = Contato::where($field, '=', $username)->first();
      if (!$contato) throw new Exception('Nenhum usuário encontrado');

    } catch (\Throwable $th) {
      $ret->msg = $th->getMessage();
      return $ret->toJson();
    }


    try {
      DB::beginTransaction();


      ContatoResetPwdTokens::where('contatoid', $contato->id)
                          ->where('processado', 0)
                          ->update([
                              'updated_at' => Carbon::now(),
                              'processado' => 2
                          ]);

      $token = new ContatoResetPwdTokens;
      $token->username = $username;
      $token->contatoid = $contato->id;
      $token->ip = \Request::getClientIp(true);
      $token->processado = 0;
      $token->expire_at = Carbon::now()->addHours(1);
      $token->codenumber = rand(100000 , 999999);
      $token->token = bcrypt( $token->expire_at->format('Ymdhis') . $token->username . $token->ip . $token->codenumber . Carbon::now());
      $token->save();

      DB::commit();

      $this->dispatch(new ContatoResetPwdRequestJob($token));

      $ret->data = [
        'username' => base64_encode($token->username),
        'token' => $token->token,
      ];
      $ret->msg = 'Código validação será enviado para ' . $token->username;
      $ret->ok = true;

    } catch (\Throwable $th) {
      DB::rollBack();
      $ret->msg = $th->getMessage();
    }
    return $ret->toJson();
  }

  public function resetpwd_checkcode(Request $request)
  {

    $ret = new RetApiController;
    try {
      $rules = [
        'username' => ['required', 'min:5', 'max:320', 'string'],
        'codenumber' => ['required', 'size:6', 'string'],
        'token' => ['required', 'string', 'min:20'],
      ];
      $captcha = env('RECAPTCHA_SITE_KEY', '') !== '';
      if ($captcha) $rules[recaptchaFieldName()] = recaptchaRuleName();
      $messages = [
          'required' => 'Campo :attribute é obrigatório!',
          'date' => 'Formato de Data inválido!',
          'max' => 'O campo :attribute, deverá ter no máximo :max caracteres!',
          'min' => 'O campo :attribute, deverá ter no mínimo :min caracteres!',
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

      $username = isset($request->username) ? $request->username : null;
      $token = isset($request->token) ? $request->token : null;
      $codenumber = isset($request->codenumber) ? $request->codenumber : null;

      if(!($codenumber > 0 ))
        throw new Exception('Código inválido');


      $token = ContatoResetPwdTokens::where('username', $username)
                                      ->where('token', $token)
                                      ->where('codenumber', $codenumber)
                                      ->where('processado', 0)
                                      ->first();
      if (!$token) throw new Exception('Código não foi encontrado ou já foi processado anteriormente');

      if ($token->expire_at < Carbon::now()) throw new Exception('Código expirado.');


       $ret->data = [
          'codenumber' => $token->codenumber,
          'token' => $token->token,
          'username' => $token->username
       ];
       $ret->ok = true;

    } catch (\Throwable $th) {
      $ret->msg = $th->getMessage();
      return $ret->toJson();
    }

    return $ret->toJson();
  }

  public function resetpwd_changepwd(Request $request)
  {

    $ret = new RetApiController;
    try {
      $rules = [
        'username'          => ['required', 'min:5', 'max:320'],
        'pwd'          => ['required', 'min:3', 'max:15'],
        'codenumber'      => ['required', 'size:6'],
        'token'      => ['required', 'min:20'],
      ];
      // $captcha = env('RECAPTCHA_SITE_KEY', '') !== '';
      // if ($captcha) $rules[recaptchaFieldName()] = recaptchaRuleName();
      $messages = [
          'required' => 'Campo :attribute é obrigatório!',
          'date' => 'Formato de Data inválido!',
          'max' => 'O campo :attribute, deverá ter no máximo :max caracteres!',
          'min' => 'O campo :attribute, deverá ter no mínimo :min caracteres!',
          // recaptchaFieldName() => 'Validação humana falhou',
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

      $pwd = $request->pwd;
      $username = $request->username;
      $codenumber = $request->codenumber;
      $token = $request->token;
      $token = ContatoResetPwdTokens::where('username', $username)
                                      ->where('codenumber', $codenumber)
                                      ->where('token', $token)
                                      ->where('processado', 0)
                                      ->first();
      if (!$token) throw new Exception('Token não foi encontrado ou já foi processado anteriormente');

      if ($token->expirado) throw new Exception('Token expirado');

    } catch (\Throwable $th) {
      $ret->msg = $th->getMessage();
      return $ret->toJson();
    }

    try {
      DB::beginTransaction();

      $contato = $token->contato;
      $contato->password = bcrypt($pwd);
      $contato->save();

      $token->processado = 1;
      $token->save();

      ContatoResetPwdTokens::where('contatoid', $contato->id)
                          ->where('processado', 0)
                          ->update([
                              'updated_at' => Carbon::now(),
                              'processado' => 2
                          ]);

      DB::commit();

      $ret->ok = true;

      // $this->dispatch(new ResetPwdChangedJob($token));

    } catch (\Throwable $th) {
      DB::rollBack();
      $ret->msg = $th->getMessage();
    }
    return $ret->toJson();
  }

}
