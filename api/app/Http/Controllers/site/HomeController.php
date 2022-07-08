<?php

namespace App\Http\Controllers\site;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\models\Empresa;
use App\models\Configuracao;
use Carbon\Carbon;
use Exception;

class HomeController extends Controller
{
    protected $tawkto;

    public function __construct(Request $request)
    {

        $suporte_tags = isset($request->suporte_tags) ? $request->suporte_tags : '';
        if($suporte_tags<>''){
            $suporte_tags=explode(";", $suporte_tags);
        }else{
            $suporte_tags =[];
        }
        

        $suporte_nome = isset($request->suporte_nome) ? $request->suporte_nome : '';
        $suporte_email = isset($request->suporte_email) ? $request->suporte_email : '';
        $suporte_razaosocial = isset($request->suporte_razaosocial) ? $request->suporte_razaosocial : '';
        $suporte_cnpj = isset($request->suporte_cnpj) ? $request->suporte_cnpj : '';

        if ($suporte_razaosocial<>'') $suporte_tags[] =  $suporte_razaosocial;
        if ($suporte_cnpj<>'') $suporte_tags[] =  $suporte_cnpj;
        
        
        $suporte_hash='';
        if (!($suporte_email=='')) {
            $suporte_apikey = 'c37cb8ec5ea03087afbec3b25fdb0aac38846ebe';
            $suporte_hash = hash_hmac("sha256", $suporte_email, $suporte_apikey);
        if ($suporte_nome=='') {
            $suporte_nome = $suporte_email;
        }
        }

        $suporte_telefones = isset($request->suporte_telefones) ? $request->suporte_telefones : '';
        if($suporte_telefones<>''){
            $suporte_telefones=explode(";", $suporte_telefones);
            foreach ($suporte_telefones as $telefone) {
                if ($telefone<>'') {
                    $suporte_tags[] = $telefone;
                }
            }
        
        }

        $tawkto = (object)[
                'tags' => $suporte_tags,
                'nome' => $suporte_nome,
                'email' => $suporte_email,
                'razaosocial' => $suporte_razaosocial,
                'cnpj' => $suporte_cnpj,
                'telefones' => $suporte_telefones,
                'hash' => $suporte_hash,
        ];
        

        $this->tawkto  =$tawkto;
        
    }

    public function index()
    {
        $empresa = Empresa::first();
        $title = 'Home';

        $redesocial = [];
        $config = Configuracao::find('SITE_REDESOCIAL_LINK');
        if($config){
            $configA  = $config->toArray(); 
            $redesocial = (object)($configA['valor']);
        }

        $site_metatags = [];
        $config = Configuracao::find('SITE_METATAGS');
        if($config){
            $configA  = $config->toArray(); 
            $site_metatags = (object)($configA['valor']);
        }        

        $contato = false;
        $config = Configuracao::find('SITE_EMAILS_CONTATO');
        if($config){
            $configA  = $config->toArray(); 
            $c =$configA['valor'];
            $contato = (count($c)>0);
        }


        $SITE_DEPOIMENTOS_ATIVO= false;
        $config = Configuracao::find('SITE_DEPOIMENTOS_ATIVO');
        if($config){
            $configA  = $config->toArray();
            $c =$configA['valor'];
            $SITE_DEPOIMENTOS_ATIVO = $c;
        }

        $SITE_FUNCIONAMENTO_ATIVO= false;
        $config = Configuracao::find('SITE_FUNCIONAMENTO_ATIVO');
        if($config){
            $configA  = $config->toArray(); 
            $c =$configA['valor'];
            $SITE_FUNCIONAMENTO_ATIVO =  $c;
        }

        $SITE_ROTAS_ATIVO= false;
        $config = Configuracao::find('SITE_ROTAS_ATIVO');
        if($config){
            $configA  = $config->toArray(); 
            $c =$configA['valor'];
            $SITE_ROTAS_ATIVO = $c;
        }

        $SITE_PRECOS_ATIVO = false;
        $config = Configuracao::find('SITE_PRECOS_ATIVO');
        if($config){
            $configA  = $config->toArray(); 
            $c =$configA['valor'];
            $SITE_PRECOS_ATIVO =  $c;
        }

        $SITE_INDICADORES_ATIVO = false;
        $config = Configuracao::find('SITE_INDICADORES_ATIVO');
        if($config){
            $configA  = $config->toArray(); 
            $c =$configA['valor'];
            $SITE_INDICADORES_ATIVO =  $c;
        }

        $SITE_INFOCAD_ATIVO = false;
        $config = Configuracao::find('SITE_INFOCAD_ATIVO');
        if($config){
            $configA  = $config->toArray(); 
            $c =$configA['valor'];
            $SITE_INFOCAD_ATIVO =  $c;
        }


        //valida se esta ativo
        try{
            $PREMATRICULA_ATIVADO = false;
            $dti = '';
            $config = Configuracao::find('PREMATRICULA_ENABLED_DTI');
            if($config){
                $dti = $config->toArray()['valor'];
                $dti = ($dti<>'' ? Carbon::createFromFormat('Y-m-d', $dti) : '') ;
            }
            $dtf = '';
            $config = Configuracao::find('PREMATRICULA_ENABLED_DTF');
            if($config){
                $dtf = $config->toArray()['valor'];
                $dtf = ($dtf<>'' ? Carbon::createFromFormat('Y-m-d', $dtf) : '') ;

            }  
            $today = Carbon::createFromFormat('Y-m-d', Carbon::now()->format('Y-m-d'));
            if($dti==''){
                throw new Exception('Data inicial vazia');
            }
            if($dtf==''){
                throw new Exception('Data final vazia');
            }
            
            if($dti > $today){
                throw new Exception('Período vigente ainda não chegou.');
            }     
            if(($dti < $today) && ($dtf < $today)){
                throw new Exception('Período de pré-matrícula terminou.');
            }                      

            $PREMATRICULA_ATIVADO = true;
        } catch (Exception $e) {
        }  
             

        $config = (object)['SITE_DEPOIMENTOS_ATIVO' => $SITE_DEPOIMENTOS_ATIVO,
                    'SITE_FUNCIONAMENTO_ATIVO' => $SITE_FUNCIONAMENTO_ATIVO,
                    'SITE_ROTAS_ATIVO' => $SITE_ROTAS_ATIVO,
                    'SITE_PRECOS_ATIVO' => $SITE_PRECOS_ATIVO,
                    'SITE_INDICADORES_ATIVO' => $SITE_INDICADORES_ATIVO,
                    'SITE_INFOCAD_ATIVO' => $SITE_INFOCAD_ATIVO,
                    'SITE_CONTATOS' => $contato,
                    'PREMATRICULA_ATIVADO' => $PREMATRICULA_ATIVADO

        ];

        $tawkto = $this->tawkto;
        return view('site.home', compact('title', 'empresa', 'redesocial', 'config', 'site_metatags', 'tawkto'));
    }

     public function suporte()
    {
        $empresa = Empresa::first();
        $title = 'Suporte Técnico';
        $tawkto = $this->tawkto;
       
        return view('site.suporte', compact('title', 'empresa', 'tawkto'));
    }

     public function teamviewer()
    {
        $empresa = Empresa::first();
        $title = 'Download Suporte Remoto TeamViewer';
        $tawkto = $this->tawkto;
       
        return view('site.teamviewer', compact('title', 'empresa', 'tawkto'));
    }

    public function chatintegrado()
    {
        $empresa = Empresa::first();
        $title = 'Chat Integrado Suporte';
        $tawkto = $this->tawkto;
       
        return view('site.chatintegrado', compact('title', 'empresa', 'tawkto'));
    }    

    

}
