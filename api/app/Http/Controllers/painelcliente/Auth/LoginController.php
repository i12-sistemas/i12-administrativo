<?php

namespace App\Http\Controllers\painelcliente\Auth;

use App\models\Contato;
use App\models\Clientes;
use App\models\ContatoCliente;
use App\models\ClienteAutologinWeb;
use App\Http\Controllers\Controller;
use App\Mail\painelcliente\ResetPassword;
use App\Mail\painelcliente\ResetPasswordCompleted;
use App\Passwordresets;
use Carbon\Carbon;
use Exception;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\RetornoApiController;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/painelcliente';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    protected function guard()
    {
        return Auth::guard('painelcliente');
    }


      
    public function auth(Request $request)  {
      $ret = new RetornoApiController;
      try {
          $credentials = $this->validate($request, [
              'login'     => 'required|min:1|max:20',
              'password'  => 'required|min:1|max:30',
          ]);
                      
          // $redirectTo = isset($request->redirect) ? $request->redirect : '/painelcliente';
          $login = isset($request->login) ? $request->login : '';
          $password = isset($request->password) ? $request->password : '';
          
          $contato = Contato::select('id', 'nome', 'cpfcnpj', 'password')->where('cpfcnpj', $login)->first();
          if(!$contato) throw new Exception("Nenhum usuário encontrado!");
          

          if (!($contato->lixeira==0)){
              throw new Exception("Usuário inativo/excluído!");
          }
          $passwordcrypt = md5('cont' . $contato->id . $password);
              
          $userdata = array(
              'cpfcnpj'     => $contato->cpfcnpj,
              'password'  => $passwordcrypt
          );            

          // attempt to do the login
          if (Auth::guard('painelcliente')->attempt($userdata)) {
              $dadoscontato = ['id'=>$contato->id, 'nome'=>$contato->nome, 'cpfcnpj'=>$contato->cpfcnpj];
              Auth::guard('painelcliente')->User = $dadoscontato;
              $ret->ok = true;
              $ret->rows = $contato;
              return $ret->toJson();
          }else{
              $ret->msg = 'Não foi autenticado.';
          }
          
      } catch (Exception $e) {
          $ret->msg =  $e->GetMessage();
      }
      return $ret->toJson();

  }


    public function logout(Request $request)
    {
        if(Auth::guard('painelcliente')->getSession()->has('cliente')){
            Auth::guard('painelcliente')->getSession()->forget('cliente');
            Auth::guard('painelcliente')->getSession()->flush();
        }
        Auth::guard('painelcliente')->logout(); // log the user out of our application
        return Redirect()->route('painelcliente.login');
    }

    public function esqueciminhasenha(Request $request)
    {
        return view('painelcliente.auth.passwords.email');
    }

    public function esqueciminhasenhatoken($token)
    {
        try {
            $tokenstatus = '';
            $tokenconfirm = Passwordresets::where('token', $token)->first();
            if (!$tokenconfirm) {
                throw new Exception('Token inválido.');
            }
            if ($tokenconfirm->checked == 1) {
                throw new Exception('Este token já foi usado anteriormente.');
            }
            if ($tokenconfirm->expired == 1) {
                throw new Exception('Token expirado.');
            }

            $associado = Associado::where('login', $tokenconfirm->loginassociado)->first();
            if (!$associado) {
                throw new Exception('Nenhum usuário encontrado.');
            }
            if (!$associado->ativo == 1) {
                throw new Exception('Usuário não esta ativo.');
            }

        } catch (Exception $e) {
            $tokenstatus = $e->getMessage();
        }
        return view('painelcliente.auth.passwords.reset', compact('token', 'tokenstatus', 'associado'));
    }

    public function resetpassword(Request $request)
    {

        $credentials = $this->validate($request, [
            'login' => 'required|min:1|max:20',
        ]);

        try {
            $login = $request->login;
            $associado = Associado::where('login', $login)->first();
            if (!$associado)
              throw new Exception('Nenhum usuário encontrado.');

            if (!$associado->ativo == 1)
                throw new Exception('Usuário não esta ativo.');

            if ($associado->email == '')
                throw new Exception('Nenhum e-mail cadastrado para este usuário. Se necessário faça contato com a administração.');

        } catch (Exception $e) {
            return Redirect()->back()->withErrors([$e->getMessage()]);
        }

        try {
            DB::beginTransaction();

            $affectedRows = Passwordresets::where('checked', 0)
                ->where('expired', 0)
                ->where('loginassociado', $associado->login)
                ->update(array('expired' => 1));

            $token = new Passwordresets;
            $token->email = $associado->email;
            $token->loginassociado = $associado->login;
            $token->createddh = Carbon::now();
            $token->token = md5($token->email . $token->loginassociado . $token->createddh . '**tokenpw');
            $token->checked = 0;
            $ins = $token->save();
            if (!$ins) {
                throw new Exception('Token não foi cadastrado.');
            }
            DB::commit();

        } catch (Exception $e) {
            DB::rollBack();
            return Redirect()->back()->withErrors([$e->getMessage()]);
        }

        try {

            $empresa = Empresa::first();
            $dados = ['token' => $token,
                'associado' => $associado,
                'empresa' => $empresa,
                'link' => route('painelcliente.esqueciminhasenhatoken', [$token->token]),
                'site' => route('site.home'),
            ];

            Mail::send(new ResetPassword($dados));
        } catch (Exception $e) {
            return Redirect()->back()->withErrors([$e->getMessage()]);

        }

        return Redirect()->back()
            ->withSuccess('E-mail com link de reset de senha foi enviado para <b>' .
                $associado->email) . '</b>';

    }

    public function resetpasswordconfirm(Request $request)
    {

        $credentials = $this->validate($request, [
            'login' => 'required|min:1|max:20',
            'password' => 'required|min:3|max:20',
            'password_confirmation' => 'required|min:3|max:20',
        ]);

        try {
            $login = $request->login;
            $token = $request->token;
            $password = $request->password;
            $password_confirmation = $request->password_confirmation;

            if ($password_confirmation != $password) {
                throw new Exception('Senhas não coincidem.');
            }

            $tokenconfirm = Passwordresets::where('token', $token)->first();
            if (!$tokenconfirm) {
                throw new Exception('Token inválido.');
            }
            if ($tokenconfirm->checked == 1) {
                throw new Exception('Este token já foi usado anteriormente.');
            }
            if ($tokenconfirm->expired == 1) {
                throw new Exception('Token expirado.');
            }

            $associado = Associado::where('login', $login)->first();
            if (!$associado)
                throw new Exception('Nenhum usuário encontrado.');

            if (!$associado->ativo == 1)
              throw new Exception('Usuário não esta ativo.');

            if ($associado->login != $tokenconfirm->loginassociado)
              throw new Exception('Usuário inválido para o token associado.');

        } catch (Exception $e) {
            return Redirect()->back()->withErrors([$e->getMessage()]);
        }

        try {
            DB::beginTransaction();

            $associado->senha = md5($password);
            $ins = $associado->save();

            if (!$ins) {
                throw new Exception('Falha ao salvar senha.');
            }

            $tokenconfirm->checked = 1;
            $ins = $tokenconfirm->save();
            if (!$ins) {
                throw new Exception('Falha ao salvar token.');
            }

            $affectedRows = Passwordresets::where('checked', 0)
                ->where('expired', 0)
                ->where('loginassociado', $associado->login)
                ->update(array('expired' => 1));

            DB::commit();

        } catch (Exception $e) {
            DB::rollBack();
            return Redirect()->back()->withErrors([$e->getMessage()]);
        }

        try {

            $empresa = Empresa::first();
            $dados = ['associado' => $associado,
                'empresa' => $empresa,
                'link' => route('painelcliente.login'),
                'site' => route('site.home'),
            ];

            Mail::send(new ResetPasswordCompleted($dados));
        } catch (Exception $e) {
            return Redirect()->back()->withSuccess(['Senha alterada com sucesso!<br>' . $e->getMessage()]);

        }

        return Redirect()->route('painelcliente.login')
            ->withSuccess('Senha alterada com sucesso!');

    }

    public function confirmaremail($token)
    {

        try {
            $title = 'Confirmação de e-mail';
            if ($token == '') {
                throw new Exception('Token inválido.');
            }

            $tokenconfirm = Associadoemailcheck::where('token', $token)->first();
            if (!$tokenconfirm) {
                throw new Exception('Token inválido.');
            }

            if ($tokenconfirm->checked == 1) {
                throw new Exception('Este token já foi usado anteriormente.');
            }
            if ($tokenconfirm->expired == 1) {
                throw new Exception('Token expirado.');
            }

            $associado = Associado::where('login', $tokenconfirm->loginassociado)->first();
            if (!$associado) {
                throw new Exception('Nenhum usuário encontrado.');
            }

        } catch (Exception $e) {
            return view('painelcliente.mensagempublica', compact('title'))->withErrors([$e->getMessage()]);
        }

        try {
            DB::beginTransaction();

            $associado->email = $tokenconfirm->email;
            $associado->emailcheckid = $tokenconfirm->id;
            $ins = $associado->save();

            if (!$ins) {
                throw new Exception('Falha ao salvar senha.');
            }

            $tokenconfirm->checked = 1;
            $tokenconfirm->checkeddh = Carbon::now();

            $ins = $tokenconfirm->save();
            if (!$ins) {
                throw new Exception('Falha ao salvar token.');
            }

            $affectedRows = Associadoemailcheck::where('checked', 0)
                ->where('expired', 0)
                ->where('loginassociado', $associado->login)
                ->update(array('expired' => 1));

            DB::commit();

        } catch (Exception $e) {
            DB::rollBack();
            return view('painelcliente.mensagempublica', compact('title'))->withErrors([$e->getMessage()]);

        }

        // try {

        //     $empresa = Empresa::first();
        //     $dados = ['associado' => $associado,
        //         'empresa' => $empresa,
        //         'link' => route('painelcliente.login'),
        //         'site' => route('site.home'),
        //     ];

        //     Mail::send(new ResetPasswordCompleted($dados));
        // } catch (Exception $e) {
        //     return Redirect()->back()->withSuccess(['Senha alterada com sucesso!<br>' . $e->getMessage()]);

        // }

        $msg = 'Obrigado por confirmar seu e-mail!';
        return view('painelcliente.mensagempublica', compact('title', 'associado', 'msg'));

    }

    public function login(Request $request)
    {
        if (Auth::guard('painelcliente')->check()) {
            return redirect()->route('painelcliente.home');
        }
        
        return view('painelcliente.auth.login');
    }


    public function setempresa(Request $request)
    {
         $ret = new RetornoApiController;
        try{
            $idempresa = isset($request->idempresa) ? intval($request->idempresa) : 0;
            if(!Auth::guard('painelcliente')->check()) throw new Exception("Login inválido. Reinicie o processo de login.");

            $cliente = Clientes::select('codcliente', 'nome', 'fantasia')
                                ->where('ativo', 1)
                                ->where('codcliente', $idempresa)
                                ->first();        
            if(!$cliente) throw new Exception("Nenhuma empresa encontrada.");

            $clienterelacao = ContatoCliente::where('idcliente', $idempresa)
                                ->where('idcontato', Auth::guard('painelcliente')->User()->id)
                                ->first();        
            if(!$clienterelacao) throw new Exception("Usuário não esta liberado para empresa selecionada");
            Auth::guard('painelcliente')->getSession()->put('cliente', $cliente);
            $ret->ok = true;
        } catch (Exception $e) {
            $ret->msg = $e->getMessage();
        }   
        return $ret->toJson();

    }

    public function listempresas(Request $request)
    {

        $ret = new RetornoApiController;
        try {
            $idcontato = isset($request->idcontato) ? $request->idcontato : 0;
            $empresas = ContatoCliente::select('clientes.nome', 'clientes.fantasia', 'clientes.cnpj','clientes.cpf', 'clientes.pessoa', 'clientes.codcliente')
                            ->where('contatocliente.idcontato', $idcontato)
                            ->join('clientes', 'contatocliente.idcliente', '=', 'clientes.codcliente')
                            ->get();   
            $list = [];
            foreach ($empresas as $value) {
                $list[] = ['codcliente'=> $value->codcliente, 
                           'nome' => utf8_encode($value->nome),
                           'fantasia' => utf8_encode($value->fantasia),
                           'cpfcnpj' => utf8_encode($value->pessoa == 'F' ? $value->cpf : $value->cnpj)
                           ] ;
            }     
            $ret->ok = true;
            $ret->rows = $list;
        } catch (Exception $e) {
            $ret->msg =  $e->GetMessage();
        }
        return $ret->toJson();

    }
    
  
    //antigo metodo desativar em 01/01/2020
    public function autoauthcreatetoken(Request $request){

        // $credentials = $this->validate($request, [
        //     'login' => 'required|min:1|max:20', 
        //     'password' => 'required|min:1|max:30',
        //     'empresa' => 'required',
        // ]);
        $ret = new RetornoApiController;
        try{
            $contato    = isset($request->contato) ? $request->contato : '';
            if($contato=='') throw new Exception("Usuário inválido.");
            $cnpj       = isset($request->cnpj) ? $request->cnpj : '';
            if($cnpj=='') throw new Exception("CNPJ inválido.");

            $host       = isset($request->host) ? $request->host : '';
            if($host=='') throw new Exception("Host  inválido.");
            
            $contato = Contato::where('cpfcnpj', $contato)->first();
            if (!$contato)
                throw new Exception("Nenhum usuário encontrado!");

            if (!($contato->lixeira==0))
                throw new Exception("Usuário inativo/excluído!");
            
            $cliente = Clientes::select('codcliente', 'nome', 'fantasia')
                                ->where('ativo', 1)
                                ->where('cnpj', $cnpj)
                                ->first();        
            if(!$cliente) throw new Exception("Nenhuma empresa encontrada com o doc. "  . $cnpj);


            $clienterelacao = ContatoCliente::where('idcliente', $cliente->codcliente)
                                ->where('idcontato', $contato->id)
                                ->first();        
            if(!$clienterelacao) throw new Exception("Usuário não esta liberado para empresa " . $cliente->nome);

            $sessaocheck = ClienteAutologinWeb::where('idcliente', $cliente->codcliente)
                                ->where('idcontato', $contato->id)
                                ->where('host', $host)
                                ->where('active', 0)
                                ->first();        
            if($sessaocheck){
                $ret->ok = true;
                $ret->id = $sessaocheck->hash;
                return $ret->toJson();
            }
            $hash = md5('clienteweb_' . $contato->cpfcnpj . $cnpj . $host . Carbon::now()->format('ddmmyyyyhhmmss'));

            DB::beginTransaction();
            $sessaobd = new ClienteAutologinWeb;
            $sessaobd->hash = $hash;
            $sessaobd->idcliente = $cliente->codcliente;
            $sessaobd->idcontato = $contato->id;
            $sessaobd->host = $host;
            $sessaobd->dhcad = Carbon::now();
            $sessaobd->active = 0;
            $saved = $sessaobd->save();
            if (!$saved){
              throw new Exception('Falha ao sessão!'); 
            }
            DB::commit();
           
            $ret->ok = true;
            $ret->id = $hash;

        } catch (Exception $e) {
            DB::rollBack();
            $ret->msg = $e->getMessage();
        }   
        return $ret->toJson();
    }      
    
    //novo
    public function autoauthcreatetokenrelacionamento(Request $request){

        // $credentials = $this->validate($request, [
        //     'login' => 'required|min:1|max:20', 
        //     'password' => 'required|min:1|max:30',
        //     'empresa' => 'required',
        // ]);
        $ret = new RetornoApiController;
        try{
            $idrelacionamento    = isset($request->idrelacionamento) ? intval($request->idrelacionamento) : 0;
            if(!($idrelacionamento>0)) throw new Exception("ID de relacionamento inválido, atualize seu cadastro.");
            
            $cnpj       = isset($request->cnpj) ? $request->cnpj : '';
            if($cnpj=='') throw new Exception("CNPJ da empresa inválido.");

            $host       = isset($request->host) ? $request->host : '';
            if($host=='') throw new Exception("Host  inválido.");

            $contato = Contato::find($idrelacionamento);
            if (!$contato) {
                throw new Exception("Nenhum usuário encontrado!");
            };      
            if (!($contato->lixeira==0)){
                throw new Exception("Usuário inativo/excluído!");
            }
            
            $cliente = Clientes::select('codcliente', 'nome', 'fantasia')
                                ->where('ativo', 1)
                                ->where('cnpj', $cnpj)
                                ->first();        
            if(!$cliente) throw new Exception("Nenhuma empresa encontrada com o doc. "  . $cnpj);


            $clienterelacao = ContatoCliente::where('idcliente', $cliente->codcliente)
                                ->where('idcontato', $contato->id)
                                ->first();        
            if(!$clienterelacao) throw new Exception("Usuário não esta liberado para empresa " . $cliente->nome);

            $sessaocheck = ClienteAutologinWeb::where('idcliente', $cliente->codcliente)
                                ->where('idcontato', $contato->id)
                                ->where('host', $host)
                                ->where('active', 0)
                                ->first();        
            if($sessaocheck){
                $ret->ok = true;
                $ret->id = $sessaocheck->hash;
                return $ret->toJson();
            }
            $hash = md5('clienteweb_' . $contato->cpfcnpj . $cnpj . $host . Carbon::now()->format('ddmmyyyyhhmmss'));

            DB::beginTransaction();
            $sessaobd = new ClienteAutologinWeb;
            $sessaobd->hash = $hash;
            $sessaobd->idcliente = $cliente->codcliente;
            $sessaobd->idcontato = $contato->id;
            $sessaobd->host = $host;
            $sessaobd->dhcad = Carbon::now();
            $sessaobd->active = 0;
            $saved = $sessaobd->save();
            if (!$saved){
                throw new Exception('Falha ao sessão!'); 
            }
            DB::commit();
           
            $ret->ok = true;
            $ret->id = $hash;

        } catch (Exception $e) {
            DB::rollBack();
            $ret->msg = $e->getMessage();
        }   
        return $ret->toJson();
    } 

    public function autoauth($contato, $cnpj, $hash, Request $request){

        // $credentials = $this->validate($request, [
        //     'login' => 'required|min:1|max:20', 
        //     'password' => 'required|min:1|max:30',
        //     'empresa' => 'required',
        // ]);
        // $ret = new RetornoApiController;
        try{

            $redirectTo = isset($request->redirect) ? $request->redirect : '/painelcliente';
            if(Auth::guard('painelcliente')->check()){
                return Redirect($redirectTo);
            }
            
            if($contato=='') throw new Exception("Usuário inválido.");
            $contato = Contato::where('cpfcnpj', $contato)->first();
            if (!$contato) 
                throw new Exception("Nenhum usuário encontrado!");
            if (!($contato->lixeira==0)){
                throw new Exception("Usuário inativo/excluído!");
            }
            if($cnpj=='') throw new Exception("CNPJ inválido.");
            $cliente = Clientes::select('codcliente', 'nome', 'fantasia')
                                ->where('ativo', 1)
                                ->where('cnpj', $cnpj)
                                ->first();        
            if(!$cliente) throw new Exception("Nenhuma empresa encontrada com o doc. "  . $cnpj);


            $clienterelacao = ContatoCliente::where('idcliente', $cliente->codcliente)
                                ->where('idcontato', $contato->id)
                                ->first();        
            if(!$clienterelacao) throw new Exception("Usuário não esta liberado para empresa " . $cliente->nome);

            if($hash=='') throw new Exception("Hash inválido.");
            $sessaobd = ClienteAutologinWeb::where('hash', $hash)->first();                  
            if(!$sessaobd) throw new Exception("Hash inválido. Cód. 009.0");
            if(!($sessaobd->active==0)) throw new Exception("Hash inválido. Cód. 009.1");
            if(!($sessaobd->idcliente==$cliente->codcliente)) throw new Exception("Hash inválido. Cód. 009.2");
            if(!($sessaobd->idcontato==$contato->id)) throw new Exception("Hash inválido. Cód. 009.3");


            DB::beginTransaction();
            $sessaobd->dhactive = Carbon::now();
            $sessaobd->active = 1;
            $saved = $sessaobd->save();
            if (!$saved){
                throw new Exception('Falha ao sessão!'); 
            }
            DB::commit();
           
            $userdata = array(
                'cpfcnpj'     => $contato->cpfcnpj,
                'password'  => $contato->password
            );
            
            // attempt to do the login
            if (Auth::guard('painelcliente')->attempt($userdata)) {
                $dadoscontato = ['id'=>$contato->id, 'nome'=>$contato->nome, 'cpfcnpj'=>$contato->cpfcnpj];
                Auth::guard('painelcliente')->User = $dadoscontato;
                $request->session()->put('cliente', $cliente);
                $request->session()->put('origem', 'autologin');
                return Redirect($redirectTo);
            }else{
                return Redirect()->route('painelcliente.login')->withErrors(['Não foi autenticado.']);
            }
        } catch (Exception $e) {
            DB::rollBack();
            return Redirect()->route('painelcliente.login')->
                    withErrors([$e->getMessage()]);
        }   
    }        
}
