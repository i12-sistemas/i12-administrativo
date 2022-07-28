<?php

namespace App\Http\Controllers\API\v1\painelcliente;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\RetApiController;
use Exception;
use App\models\OrdemServico;
use Illuminate\Support\Facades\DB;
use App\models\Interacao;
use App\models\Clientes;
use App\models\Contato;
use App\models\InteracaoOS;
use App\models\InteracaoCliente;
use Carbon\Carbon;
use App\models\Usuario;
use App\models\OSLeitura;
use App\models\AvaliacaoPergunta;
use App\models\AvaliacaoRespOS;
use App\models\Empresa;
use App\models\InteracaoAnexoLink;
use App\models\InteracaoAnexo;
use Illuminate\Support\Facades\Storage;

use App\Jobs\adm\BackupDownloadJob;

use App\Jobs\painelcliente\InteracaoAddClienteJob;

use App\models\OSLocalAtendimento;
use App\models\Configuracao;
use App\models\User;
use App\models\ContatoCliente;
use Illuminate\Support\Str;

class OrdemServicoController extends Controller
{
    // private $STORAGE_MODO;

    // public function __construct()
    // {
    //   $this->STORAGE_MODO = env('STORAGE_MODO', 'public');
    // }

    public function list(Request $request) {
        
        $ret = new RetApiController;
        try{

            $contato = session('contato');
            if(!$contato) throw new Exception("Contato não encontrado");
            $cliente = session('cliente');
            if(!$cliente) throw new Exception("Cliente não encontrado");
            
            raiseIsOperador();

            
            $find = isset($request->find) ? strval($request->find) : '';
            // $pendenteresposta = isset($request->pendenteresposta) ? intval($request->pendenteresposta) : -1;
            // $naolido = isset($request->naolido) ? intval($request->naolido) : -1;
            $situacao = $request->has('situacao') ? strval($request->situacao) : 'emabertos';
            $situacao = Str::lower($situacao) ;
            // if (($situacao != 'encerrada') && ($situacao != 'todos')) $situacao = 'aberta';

 
            // if(!(($pendenteresposta==0)||($pendenteresposta==1))){
            //     $pendenteresposta==-1;
            // }            
            // $mostrartodosempresa = isset($request->mostrartodosempresa) ? intval($request->mostrartodosempresa) : 0;
            
            // $situacao = isset($request->situacao) ? $request->situacao : '';
            // if(!(($situacao=='')||($situacao=='Encerrada'))){
            //     $situacao=='Aberta';
            // }

            $perpage = isset($request->perpage) ? $request->perpage : 20;


            // //40000375, "Permite ver chamado com valores(R$)"
            // $permitevervalores = $contato->checkpermite($idcliente, '40000375') ? 1 : 0;
            // //40000374, "Permite ver chamado de todos os usuários da empresa
            // $permitevertodos = $contato->checkpermite($idcliente, '40000374');
            // if(!$permitevertodos) $mostrartodosempresa = 0;
            // $permitevervalores = $permitevervalores ? 1 : 0;

            $dataset = OrdemServico::select('osordem.*')
                  ->with('fase', 'categoria', 'classificacao', 'ultimainteracao')
                  ->leftJoin('interacao as interacaoultimapublica', 'osordem.idinteracaoultimapublica', '=', 'interacaoultimapublica.id')
                  ->where('osordem.idclientecontato', '=', $contato->id)
                  ->where('osordem.idcliente', '=', $cliente->codcliente)
                  ->where('osordem.modo', 'OS')
                  ->when($request->has('situacao') && ($situacao ? $situacao !== '' : false), function ($query) use ($situacao) {
                    switch ($situacao) {
                      case 'encerrada':
                        return $query->where('osordem.situacao', '=', 'Encerrada');
                        break;
                      case 'emabertos':
                        return $query->where('osordem.situacao', '=', 'Aberta');
                        break;
                      case 'pendenteresposta':
                        return $query->where('osordem.situacao', '=', 'Aberta')
                                  ->where('osordem.pendentecliente', '=', 1);
                        break;
                      case 'naolidos':
                        return $query->whereNull('osordem.contato_leitura_at')
                            ->whereRaw('if(osordem.situacao="Aberta", true, datediff(now(), dtFechamento)<= 15 )');
                        break;
                    }
                  })
                  ->when($request->has('find') && ($find ? $find !== '' : false), function ($query) use ($find) {
                    return $query->where(function($query2) use ($find) {
                      $qLiked = '%' . $find . '%';
                      return $query2->Where('osordem.problemareclamado', 'like', $qLiked)
                            ->orWhere('osordem.problemaconstatado', 'like', $qLiked)
                            ->orWhere('osordem.conclusao', 'like', $qLiked)
                            ->orWhere('interacaoultimapublica.texto', 'like', $qLiked)
                            ;
                    });                    
                  })
                  // ->whereRaw('if(?<0, true, if(osordem.situacao="Aberta", true, datediff(now(), dtFechamento)<=7  ))', [$naolido])

                  // ->whereRaw('if(?=1, true, 
                  //                 (ifnull(osordem.totalservico,0)=0 AND ifnull(osordem.totalproduto,0)=0 and
                  //                  ifnull(osordem.totaldeslocamento,0)=0 and ifnull(osordem.total,0)=0)
                  //             )', [$permitevervalores])
                  

                  // ->whereRaw('if(?="", true, osordem.situacao=?)', [$situacao, $situacao])
                  // ->whereRaw('if(?<0, true, osfases.pendentecliente=?)', [$pendenteresposta, $pendenteresposta])
                  // ->Where(function ($query) use ($search) {
                  //     $query->orWhere('osordem.problemareclamado', 'like', "%{$search}%")
                  //         ->orWhere('osordem.id', $search);
                  // })
                  ->orderBy('interacaoultimapublica.datahora', 'desc')
                  ->orderBy('osordem.dtabertura', 'desc')
                  ->paginate($perpage);

            $dados =[];
            foreach ($dataset as $row) {
              $dados[] = $row->exportCliente(false);
            }
            $ret->collection = $dataset;
            $ret->data = $dados;
            $ret->ok = true;

        } catch (Exception $e) {
            $ret->msg = $e->getMessage();
        }

        return $ret->toJson();
    }


    public function detalhe(Request $request, $numero) {
      $ret = new RetApiController;
      try{

        $contato = session('contato');
        if(!$contato) throw new Exception("Contato não encontrado");
        $cliente = session('cliente');
        if(!$cliente) throw new Exception("Cliente não encontrado");

        raiseIsOperador();
                  
        $atendimento = OrdemServico::select('osordem.*')
                    ->where('osordem.idclientecontato', '=', $contato->id)
                    ->where('osordem.idcliente', '=', $cliente->codcliente)
                    ->where('osordem.modo', 'OS')                      
                    ->where('osordem.id', '=', $numero)
                    ->first();
                    
        if(!$atendimento) throw new Exception('Chamado não foi encontrado');
        $dados = $atendimento->exportCliente(true);
        
        $ret->ok = true;
        $ret->data = $dados;
      } catch (Exception $e) {
          $ret->msg = $e->getMessage();
      }
      return $ret->toJson();
    }   
    
    public function interacaoAdd(Request $request, $numero) {
      $ret = new RetApiController;
      try{

        $contato = session('contato');
        if(!$contato) throw new Exception("Contato não encontrado");
        $cliente = session('cliente');
        if(!$cliente) throw new Exception("Cliente não encontrado");

        raiseIsOperador();

        $mensagem = isset($request->mensagem) ? strip_tags(strval($request->mensagem)) : '';
        if ((Str::length($mensagem) < 1) || (Str::length($mensagem) >  10000)) throw new Exception("Mensagem inválida. Mínimo de 1 e máximo de 10.000 caracteres");
              


        $atendimento = OrdemServico::select('osordem.*')
                    ->where('osordem.idclientecontato', '=', $contato->id)
                    ->where('osordem.idcliente', '=', $cliente->codcliente)
                    ->where('osordem.modo', 'OS')                      
                    ->where('osordem.id', '=', $numero)
                    ->first();

        if(!$atendimento) throw new Exception("Nenhum atendimento encontrado");
        if($atendimento->situacao === 'Encerrada') throw new Exception("Este atendimento está encerrado");
          
      } catch (Exception $e) {
        $ret->msg = $e->getMessage();
        return $ret->toJson();
      }

      try{
        DB::beginTransaction();

        $interacao = new Interacao;
        $interacao->datahora = Carbon::now();
        $interacao->datahorafinal = Carbon::now();
        $interacao->forma = 'PAINEL CLIENTE';
        $interacao->categoria = '';
        $interacao->classificacao = '';
        $interacao->texto = $mensagem;
        $interacao->idcontatocad = $contato->id;
        $interacao->idcontatoalt = $contato->id;
        $interacao->autoregistro = 0;
        $interacao->idcontatoacao = $contato->id;
        // $interacao->idcontatopara = $token;
        $interacao->restrito = 0;
        $interacao->origem = 'C';
        $interacao->excluido = 0;
        $interacao->ip = \Request::getClientIp(true);
        $saved = $interacao->save();
        if (!$saved) throw new Exception('Falha ao salvar mensagem!'); 

        $interacaoOS = new InteracaoOS;
        $interacaoOS->idinteracao = $interacao->id;
        $interacaoOS->idos = $atendimento->id;
        $saved = $interacaoOS->save();
        if (!$saved) throw new Exception('Falha ao salvar mensagem!'); 

        $interacaoCliente = new InteracaoCliente;
        $interacaoCliente->idinteracao = $interacao->id;
        $interacaoCliente->idcliente = $atendimento->idcliente;
        $saved = $interacaoCliente->save();
        if (!$saved) throw new Exception('Falha ao salvar mensagem!'); 

        $atendimento->contato_leitura_at = Carbon::now();
        $atendimento->pendentecliente = 0;
        $atendimento->save();

        DB::commit();

        $ret->ok = true;
        $ret->id = $interacao->id;
        $ret->data = $interacao->exportCliente(false, true);
      } catch (Exception $e) {
          DB::rollBack();
          $ret->msg = $e->getMessage();
      }   

      //send mail
      try {
        $job = new InteracaoAddClienteJob($atendimento->id, $interacao->id, 'Nova interação');
        dispatch($job);
      } catch (Exception $e) {
        \Log::error('send mail no controller: ' . $e->getMessage());
      }        

      return $ret->toJson();
  }    



  public function setreadchamado(Request $request, $numero) {
    $ret = new RetApiController;
    try{

      $contato = session('contato');
      if(!$contato) throw new Exception("Contato não encontrado");
      $cliente = session('cliente');
      if(!$cliente) throw new Exception("Cliente não encontrado");

      raiseIsOperador();

      $lido = $request->lido ? strVal($request->lido) : 'sim';
      if (Str::lower($lido) !== 'lido') $lido = 'naolido';
            


      $atendimento = OrdemServico::select('osordem.*')
                  ->where('osordem.idclientecontato', '=', $contato->id)
                  ->where('osordem.idcliente', '=', $cliente->codcliente)
                  ->where('osordem.modo', 'OS')                      
                  ->where('osordem.id', '=', $numero)
                  ->first();

      if(!$atendimento) throw new Exception("Nenhum atendimento encontrado");
      if($atendimento->situacao === 'Encerrada') throw new Exception("Este atendimento está encerrado");
      if($atendimento->idclientecontato !== $contato->id) throw new Exception("Contato diferente do responsável da OS");
        

      // $leitura = null;
      // if($idleitura>0){
      //     $leitura = OSLeitura::find($idleitura);
      // }
          
      } catch (Exception $e) {
          $ret->msg = $e->getMessage();
          return $ret->toJson();
      }

        try{
          DB::beginTransaction();
          
          // if(!(($idleitura>0)&&($leitura))){
          //     $leitura = new OSLeitura;
          //     $leitura->ip = \Request::getClientIp(true);
          //     $leitura->dataleitura = Carbon::now();
          //     $leitura->idcontato = $idcontato;
          //     $leitura->idos = $chamado->id;
          //     $leitura->url = $url;
          // }           
          
          // $leitura->temposec = $leitura->dataleitura->diffInSeconds(Carbon::now()); 
          // $saved = $leitura->save();
          // if (!$saved){
          //     throw new Exception('Falha ao salvar mensagem!'); 
          // }

          // $contato = Contato::find($chamado->idclientecontato);
          // if($contato){
          //     $contato->i12ultimaatualizacao = Carbon::now();
          //     $contato->save();
          // }
          // $cliente = Clientes::find($chamado->idcliente);
          // if($cliente){
          //     $cliente->i12ultimaatualizacao = Carbon::now();
          //     $cliente->save();
          // }     
          
          $atendimento->contato_leitura_at = Carbon::now();
          $atendimento->save();       

          DB::commit();

          $ret->ok = true;
      } catch (Exception $e) {
          DB::rollBack();
          $ret->msg = $e->getMessage();
      }   

      return $ret->toJson();
    }


  public function contadores(Request $request) {
    $ret = new RetApiController;
    try{
        $contato = session('contato');
      if(!$contato) throw new Exception("Contato não encontrado");
      $cliente = session('cliente');
      if(!$cliente) throw new Exception("Cliente não encontrado");

      raiseIsOperador();
  
          
      $emabertos = OrdemServico::where('osordem.idclientecontato', $contato->id)
                      ->where('osordem.idcliente', $cliente->codcliente)
                      ->where('osordem.modo', 'OS')
                      ->where('osordem.situacao', 'Aberta')
                      // ->whereRaw('if(?=1, true, 
                      //                 (ifnull(osordem.totalservico,0)=0 AND ifnull(osordem.totalproduto,0)=0 and
                      //                   ifnull(osordem.totaldeslocamento,0)=0 and ifnull(osordem.total,0)=0)
                      //             )', [$permitevervalores])                            
                      ->count();

      $pendenteresposta = OrdemServico::where('osordem.idclientecontato', $contato->id)
                        ->where('osordem.idcliente', $cliente->codcliente)
                        ->where('osordem.modo', '=', 'OS')
                        ->where('osordem.situacao', '=', 'Aberta')
                        ->where('osordem.pendentecliente', '=', 1)
                        //  ->whereRaw('if(?=1, true, 
                        //                 (ifnull(osordem.totalservico,0)=0 AND ifnull(osordem.totalproduto,0)=0 and
                        //                  ifnull(osordem.totaldeslocamento,0)=0 and ifnull(osordem.total,0)=0)
                        //             )', [$permitevervalores]) 
                        ->orderBy('osordem.dtabertura', 'desc')
                        ->count();


      $naolidos = OrdemServico::where('osordem.idclientecontato', $contato->id)
                      ->where('osordem.idcliente', $cliente->codcliente)
                      ->where('osordem.modo', 'OS')
                      ->whereNull('osordem.contato_leitura_at')
                      ->whereRaw('if(osordem.situacao="Aberta", true, datediff(now(), dtFechamento)<=15 )')
                        // ->whereRaw('if(?=1, true, 
                        //               (ifnull(osordem.totalservico,0)=0 AND ifnull(osordem.totalproduto,0)=0 and
                        //                 ifnull(osordem.totaldeslocamento,0)=0 and ifnull(osordem.total,0)=0)
                        //           )', [$permitevervalores]) 
                      ->count();

                          

          // $naolidos = 0;
          // if($novarespostas){
          //     $naolidos =  $novarespostas->count();
          // }                          
                          
          

          // da empresa
          // if($permitevertodos){
          //     $emabertos = OrdemServico::where('osordem.idcliente', $idcliente)
          //                     ->where('osordem.modo', 'OS')
          //                     ->where('osordem.situacao', 'Aberta')
          //                      ->whereRaw('if(?=1, true, 
          //                                 (ifnull(osordem.totalservico,0)=0 AND ifnull(osordem.totalproduto,0)=0 and
          //                                  ifnull(osordem.totaldeslocamento,0)=0 and ifnull(osordem.total,0)=0)
          //                             )', [$permitevervalores]) 
          //                     ->count();

          //     $pendenteresposta = OrdemServico::leftJoin('osfases', 'osordem.idfase', '=', 'osfases.id')
          //                     ->where('osordem.idcliente', $idcliente)
          //                     ->where('osordem.modo', 'OS')
          //                     ->where('osordem.situacao', 'Aberta')
          //                     ->where('osfases.pendentecliente', 1)
          //                      ->whereRaw('if(?=1, true, 
          //                                 (ifnull(osordem.totalservico,0)=0 AND ifnull(osordem.totalproduto,0)=0 and
          //                                  ifnull(osordem.totaldeslocamento,0)=0 and ifnull(osordem.total,0)=0)
          //                             )', [$permitevervalores]) 
          //                     ->orderBy('osordem.dtabertura', 'desc')
          //                     ->count();                            



          //     $novarespostas = OrdemServico::select(DB::raw('max(osleitura.dataleitura) as d, max(interacao.datahora) as m'))
          //                             ->leftJoin('interacaoos', 'osordem.id', '=', 'interacaoos.idos')
          //                             ->leftJoin('interacao', function($join)  {
          //                                 $join->on('interacaoos.idinteracao', '=', 'interacao.id')
          //                                 ->where('interacao.origem', '<>', 'C')
          //                                 ->where('interacao.restrito', '=', 0);
          //                             })
          //                         ->leftJoin('osleitura', function($join) use ($idcontato)  {
          //                             $join->on('osordem.id', '=', 'osleitura.idos')
          //                                 ->where('osleitura.idcontato', $idcontato);
          //                         })   
          //                     ->where('osordem.idcliente', $idcliente)
          //                     ->where('osordem.modo', 'OS')
          //                     ->whereRaw('if(osordem.situacao="Aberta", true, datediff(now(), dtFechamento)<=7  )')
          //                      ->whereRaw('if(?=1, true, 
          //                                 (ifnull(osordem.totalservico,0)=0 AND ifnull(osordem.totalproduto,0)=0 and
          //                                  ifnull(osordem.totaldeslocamento,0)=0 and ifnull(osordem.total,0)=0)
          //                             )', [$permitevervalores]) 
          //                     ->orderBy('osordem.dtabertura', 'desc')
          //                     ->groupBy('osordem.id')
          //                     ->havingRaw('if(max(osleitura.dataleitura) is null, true, max(osleitura.dataleitura) < max(interacao.datahora))')
          //                     ->get();
          //     $naolidos = 0;
          //     if($novarespostas){
          //         $naolidos =  $novarespostas->count();
          //     }
          //     $empresachamados = [   'emabertos' => $emabertos,
          //                     'pendenteresposta' => $pendenteresposta,
          //                     'naolidos'  => $naolidos,
          //                 ];                
          // }else{
          //     $empresachamados=[];
          // }



                          

      


          // $chamados = OrdemServico::select('osordem.id', 'osordem.problemareclamado','osordem.dtabertura', 'osordem.idfase', 'osordem.situacao', 'osordem.idclientecontato')
          //                 ->leftJoin('osfases', 'osordem.idfase', '=', 'osfases.id')
          //                 ->whereRaw('if(?<0, true, osordem.idclientecontato=?)', [$idcontato, $idcontato])
          //                 ->where('osordem.idcliente', $idcliente)
          //                 ->where('osordem.modo', 'OS')
          //                 ->whereRaw('if(?="", true, osordem.situacao=?)', [$situacao, $situacao])
          //                 ->whereRaw('if(?<0, true, osfases.pendentecliente=?)', [$pendenteresposta, $pendenteresposta])
          //                 ->Where(function ($query) use ($search) {
          //                     $query->orWhere('osordem.problemareclamado', 'like', "%{$search}%")
          //                         ->orWhere('osordem.id', $search);
          //                 })
          //                 ->orderBy('osordem.dtabertura', 'desc')
          //                 ->paginate($limit);

          
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
  

  public function addchamado(Request $request) {
    $ret = new RetApiController;
    try{
      $contato = session('contato');
      if(!$contato) throw new Exception("Contato não encontrado");
      $cliente = session('cliente');
      if(!$cliente) throw new Exception("Cliente não encontrado");
      if($cliente->ativo !==1) throw new Exception("Empresa desativada");

      raiseIsOperador();

      $mensagem = isset($request->mensagem) ? strVal($request->mensagem) : '';
      $mensagem = trim($mensagem);
      if(strlen($mensagem)<10) throw new Exception("Informe no mínimo 10 caracteres no detalhe");
        
      $prioridade = isset($request->prioridade) ? intVal($request->prioridade) : 1;
      if (($prioridade < 1) || ($prioridade > 3)) throw new Exception("Prioridade inválida");

      switch ($prioridade) {
        case 2:
          $prioridadedesc = 'Média';
          break;
        case 3:
          $prioridadedesc = 'Alta';
          break;
        default:
          $prioridadedesc = 'Normal';
          break;
      }

      $prioridadedesc = 'Prioridade informada pelo cliente: ' . $prioridade . '-' . $prioridadedesc;
              
      $empresa = Empresa::where('cnpj', '=', ENV('CNPJ_I12'))->first();
      if (!$empresa) throw new Exception('Empresa (i12) não foi encontrada');
      
      $localpadrao = OSLocalAtendimento::where('local', 'PAINEL CLIENTE')->first();
      if(!$localpadrao) throw new Exception("Local de atendimento padrão 'PAINEL DO CLIENTE' não foi configurado.");
      if(!($localpadrao->ativo==1)) throw new Exception("Local de atendimento padrão 'PAINEL DO CLIENTE' não está ativo.");


      $userpadrao = 0;
      $config = Configuracao::find('PAINELCLIENTE_NOVOCHAMADO_IDUSUARIO');
      if($config){
          $configA  = $config->toArray(); 
          $userpadrao =  intval($configA['valor']);
      }
      if(!($userpadrao>0)) throw new Exception("Nenhum usuário padrão configurado. [PAINELCLIENTE_NOVOCHAMADO_IDUSUARIO].");
      $usuariopadrao = Usuario::find($userpadrao);
      if(!$usuariopadrao) throw new Exception("Usuário padrão não foi encontrado. Cód.: " . $userpadrao);
      if(!($usuariopadrao->ativo==1)) throw new Exception("Usuário padrão não esta ativo. Cód.: " . $userpadrao);


      $CENTROCUSTOID = -1;
      $config = Configuracao::find('OS_CENTROCUSTODEPTO_PADRAO');
      if($config){
        $configA  = $config->toArray(); 
        $CENTROCUSTOID =  intval($configA['valor']);
      }


      $PLANOCONTAID = -1;
      $config = Configuracao::find('OS_CENTROCUSTO_PADRAO');
      if($config){
        $configA  = $config->toArray(); 
        $PLANOCONTAID =  intval($configA['valor']);
      }


    } catch (Exception $e) {
        $ret->msg = $e->getMessage();
        return $ret->toJson();
    }

     try{
        DB::beginTransaction();

        $chamado = new OrdemServico;
        $chamado->idcliente = $cliente->codcliente;
        $chamado->idclientecontato = $contato->id;
        $chamado->dtabertura = Carbon::now();
        $chamado->tipo = 'PAINEL CLIENTE';
        $chamado->problemareclamado = $mensagem;
        $chamado->totalservico = 0;
        $chamado->totalproduto = 0;
        $chamado->totaldeslocamento = 0;
        $chamado->acres = 0;
        $chamado->total = 0;

        if ($PLANOCONTAID > 0) $chamado->codcentrocusto = $PLANOCONTAID;
        if ($CENTROCUSTOID > 0) $chamado->codcentrocustodepto = $CENTROCUSTOID;

        $chamado->pendentecliente = 0;
        $chamado->prioridade = $prioridade;
        $chamado->datavalidade = Carbon::now()->addHour(72);
        $chamado->contato_leitura_at = Carbon::now();
        $chamado->ultalteracao = Carbon::now();
        $chamado->modo = 'OS';
        $chamado->situacao = 'Aberta';
        $chamado->avaliado = 0;
        $chamado->idempresa = $empresa->codempresa;

        $chamado->idusuario = $usuariopadrao->codusuario;
        $chamado->idusuariovenda = $usuariopadrao->codusuario;

        $saved = $chamado->save();
        if (!$saved){
            throw new Exception('Falha ao salvar chamado!'); 
        }


        $interacao = new Interacao;
        $interacao->datahora = Carbon::now();
        $interacao->datahorafinal = Carbon::now();
        $interacao->forma = 'PAINEL CLIENTE';
        $interacao->categoria = '';
        $interacao->classificacao = '';
        $interacao->texto =  $mensagem . ' - ' . $prioridadedesc;
        $interacao->idcontatocad = $contato->id;
        $interacao->idcontatoalt = $contato->id;
        $interacao->autoregistro = 0;
        $interacao->idcontatoacao = $contato->id;
        $interacao->restrito = 0;
        $interacao->origem = 'C';
        $interacao->excluido = 0;
        $interacao->ip = \Request::getClientIp(true);
        $saved = $interacao->save();
        if (!$saved){
            throw new Exception('Falha ao salvar mensagem!'); 
        }

        $interacaoOS = new InteracaoOS;
        $interacaoOS->idinteracao = $interacao->id;
        $interacaoOS->idos = $chamado->id;
        $saved = $interacaoOS->save();
        if (!$saved){
            throw new Exception('Falha ao salvar mensagem!'); 
        }            

        $interacaoCliente = new InteracaoCliente;
        $interacaoCliente->idinteracao = $interacao->id;
        $interacaoCliente->idcliente = $chamado->idcliente;
        $saved = $interacaoCliente->save();
        if (!$saved){
            throw new Exception('Falha ao salvar mensagem!'); 
        }     


        DB::commit();
        $ret->ok = true;
        $ret->id = $chamado->id;
    } catch (Exception $e) {
        DB::rollBack();
        $ret->msg = $e->getMessage();
        return $ret->toJson();
    }   

    //send mail
    try {
      $job = new InteracaoAddClienteJob($chamado->id, $interacao->id, 'NOVA SOLICITAÇÃO');
      dispatch($job);
    } catch (Exception $e) {
      \Log::alert($e->getMessage());
    }       

    return $ret->toJson();
  }    



    // public function encerrachamadousuario(Request $request) {
    //     $ret = new RetornoApiController;
    //     try{
    //         $idchamado = isset($request->idchamado) ? intval($request->idchamado) : 0;
    //         $idcontato = isset($request->idcontato) ? intval($request->idcontato) : 0;
    //         $chamado = OrdemServico::select('osordem.id', 'osordem.idfase', 'osordem.situacao', 'osordem.idclientecontato', 'osordem.idcliente', 'osordem.idusuariovenda')
    //                         ->where('osordem.modo', 'OS')
    //                         ->where('osordem.id', $idchamado)
    //                         ->first();
    //         if(!$chamado) throw new Exception("Nenhum chamado encontrado");
    //         if($chamado->situacao=='Encerrada') throw new Exception("Chamado está encerrado");
    //     } catch (Exception $e) {
    //         $ret->msg = $e->getMessage();
    //         return $ret->toJson();
    //     }

    //      try{
    //         DB::beginTransaction();

    //         $interacao = new Interacao;
    //         $interacao->datahora = Carbon::now();
    //         $interacao->datahorafinal = Carbon::now();
    //         $interacao->forma = 'PAINEL CLIENTE';
    //         $interacao->categoria = '';
    //         $interacao->classificacao = '';
    //         $interacao->texto = 'Chamado encerrado pelo próprio usuário';
    //         $interacao->idcontatocad = $idcontato;
    //         $interacao->idcontatoalt = $idcontato;
    //         $interacao->autoregistro = 0;
    //         $interacao->idcontatoacao = $idcontato;
    //         $interacao->restrito = 0;
    //         $interacao->origem = 'C';
    //         $interacao->excluido = 0;
    //         $interacao->ip = \Request::getClientIp(true);
    //         $saved = $interacao->save();
    //         if (!$saved){
    //             throw new Exception('Falha ao salvar mensagem!'); 
    //         }

    //         $interacaoOS = new InteracaoOS;
    //         $interacaoOS->idinteracao = $interacao->id;
    //         $interacaoOS->idos = $chamado->id;
    //         $saved = $interacaoOS->save();
    //         if (!$saved){
    //             throw new Exception('Falha ao salvar mensagem!'); 
    //         }            

    //         $interacaoCliente = new InteracaoCliente;
    //         $interacaoCliente->idinteracao = $interacao->id;
    //         $interacaoCliente->idcliente = $chamado->idcliente;
    //         $saved = $interacaoCliente->save();
    //         if (!$saved){
    //             throw new Exception('Falha ao salvar mensagem!'); 
    //         }     

    //         $chamado->dtfechamento = Carbon::now();
    //         $chamado->situacao = 'Encerrada';
    //         $saved = $chamado->save();
    //         if (!$saved){
    //             throw new Exception('Falha ao salvar chamado como encerrado!'); 
    //         }     

            


    //         DB::commit();
    //         $ret->ok = true;
    //         $ret->id = $interacao->id;
    //     } catch (Exception $e) {
    //         DB::rollBack();
    //         $ret->msg = $e->getMessage();
    //     }   


    //     //send mail
    //     try {
    //         $contato = null;
    //         if($chamado->usuario){
    //             $emails[] = (object)['email' => $chamado->usuario->email, 
    //                                 'urlpublico' => ''
    //                         ];
                
    //             $contato = (object)[
    //                         'nome' => $chamado->usuario->nome,
    //                         'emails' => $emails
    //                     ];
    //         }  
    //         if($contato){
    //             foreach ($contato->emails as $email) {
                    
    //                 if($email->email<>''){
    //                     $destinatario = (object)[   'nome' => $contato->nome,
    //                                                 'email' => $email->email,
    //                                                 'link' => $email->urlpublico
    //                                             ];
                      
    //                     $job = new ChamadosJob($chamado, $interacao, $destinatario, 'Chamado encerrado pelo cliente/contato');
    //                     dispatch($job);
    //                 }
    //             }
    //         }  
    //         $ret->ok = true;
    //     } catch (Exception $e) {
    //         return $ret->msg = $e->getMessage();
    //     }        
        

    //     return $ret->toJson();
    // }  



    // public function avaliachamado(Request $request) {
        
    //     $ret = new RetornoApiController;
    //     try{
    //         $idchamado = isset($request->idchamado) ? intval($request->idchamado) : 0;
    //         $idcontato = isset($request->idcontato) ? intval($request->idcontato) : 0;
    //         $idpergunta = isset($request->idpergunta) ? intval($request->idpergunta) : 0;
    //         $valorresp = isset($request->valorresp) ? floatval($request->valorresp) : 0;
    //         $resplivre = isset($request->resplivre) ? $request->resplivre : '';
    //         $resplivre = trim($resplivre);

    //         $finalizar = (isset($request->finalizar) ? intval($request->finalizar) : 0 == 1);


    //         $chamado = OrdemServico::select('osordem.id', 'osordem.idfase', 'osordem.situacao', 'osordem.idclientecontato', 'osordem.idcliente')
    //                         ->where('osordem.modo', 'OS')
    //                         ->where('osordem.id', $idchamado)
    //                         ->first();

    //         if(!$chamado) throw new Exception("Nenhum chamado encontrado");
    //         if(!($chamado->situacao=='Encerrada')) throw new Exception("Chamado não está encerrado");

    //         $pergunta = AvaliacaoPergunta::find($idpergunta);
    //         if(!$pergunta) throw new Exception("Nenhum questionário encontrado");
    //         if(!($pergunta->ativo==1)) throw new Exception("Questionário não está ativo");
    //         if(!($pergunta->localaplicacao=='OS')) throw new Exception("Questionário não está ativo para chamado");
            
    //     } catch (Exception $e) {
    //         $ret->msg = $e->getMessage();
    //         return $ret->toJson();
    //     }

    //      try{
    //         DB::beginTransaction();

    //         $resp = new AvaliacaoRespOS;
    //         $resp->dhavaliacao = Carbon::now();
    //         $resp->idpergunta = $pergunta->id;
    //         $resp->idos = $chamado->id;
    //         $resp->idcontato = $idcontato;
    //         $resp->resplivre = $resplivre;
    //         $resp->valor = $valorresp;
    //         $resp->ip = \Request::getClientIp(true);
    //         $saved = $resp->save();
    //         if (!$saved){
    //             throw new Exception('Falha ao salvar resposta!'); 
    //         }

    //         if($finalizar){
    //             $chamado->avaliado = 1;
    //             $saved = $chamado->save();
    //             if (!$saved){
    //                 throw new Exception('Falha ao salvar chamado avaliado!'); 
    //             }    
    //         }

    //         DB::commit();
    //         $ret->ok = true;
    //     } catch (Exception $e) {
    //         DB::rollBack();
    //         $ret->msg = $e->getMessage();
    //     }   

    //     return $ret->toJson();
    // }    

    
    // public function avaliaperguntalist(Request $request) {
        
    //     $ret = new RetornoApiController;
    //     try{
    //         $tipo = isset($request->tipo) ? $request->tipo : 'OS';
    //         $idchamado = isset($request->idchamado) ? intval($request->idchamado) : 0;

    //         $perguntas = AvaliacaoPergunta::select('avaliacaopergunta.id', 'avaliacaopergunta.pergunta', 'avaliacaopergunta.opcao1', 'avaliacaopergunta.opcao2',
    //                                                 'avaliacaopergunta.opcao3', 'avaliacaopergunta.opcao4', 'avaliacaopergunta.opcao5', 
    //                                                 'avaliacaopergunta.permiteresplivre', 'avaliacaopergunta.obrigatorioresplivre', 'avaliacaopergunta.qtdeopcao'  )
    //                         ->leftJoin('avaliacaorespos', function($join) use($idchamado) {
    //                                 $join->on('avaliacaopergunta.id', '=', 'avaliacaorespos.idpergunta')
    //                                     ->where('avaliacaorespos.idos', $idchamado);
    //                             })
                                
    //                         ->where('avaliacaopergunta.localaplicacao','=', $tipo)
    //                         ->where('avaliacaopergunta.ativo', 1)
    //                         ->whereRaw('avaliacaorespos.id is null')
    //                         ->orderBy('avaliacaopergunta.ordem')
    //                         ->get();

    //         $ret->ok = true;
    //         $ret->rows = $perguntas;
    //     } catch (Exception $e) {
    //         $ret->msg = $e->getMessage();
    //     }

    //     return $ret->toJson();
    // }
 
    // public function sendmailchamado(Request $request) {
    //     $ret = new RetornoApiController;
    //     try{
    //         $id = isset($request->id) ? $request->id : 0;
    //         $hash = isset($request->hash) ? $request->hash : '';
    //         $email = isset($request->email) ? $request->email : '';
    //         $chamado = OrdemServico::select('osordem.id', 'osordem.problemareclamado','osordem.dtabertura', 'osordem.dtfechamento', 
    //                                         'osordem.situacao', 'osordem.idclientecontato', 
    //                                         'osordem.idcliente', 'osordem.tipo', 'osordem.avaliado', 'osordem.idusuariovenda')
    //                     ->where('osordem.id', $id)
    //                     ->first();
    //         $dados = $chamado->toArray();
    //         if(!$chamado) throw new Exception('Chamado não foi encontrado');
    //         $hashcheck = md5($chamado->id . $chamado->dtabertura->format('Y-m-d H:i:s') . 'OS');
    //         if($hashcheck<>$hash) throw new Exception('Hash inválido - ' . $hashcheck  . '- '. $hash);

    //         $interacao = $chamado->interacoespublicas->first();
    //         if($interacao){
    //             $interacao = $interacao->interacao;
    //         }else{
    //             throw new Exception('Nenhum interação no chamado.');
    //         }

    //         // contato
    //         $contato = null;
    //         if($chamado->contato){
    //             $emails[] = (object)['email' => $email, 
    //                         'urlpublico' => url('/painelcliente/') . '/chamado/show/publico/' . 
    //                                             base64_encode($chamado->id) . '/' . 
    //                                             md5($chamado->id . $chamado->dtabertura->format('Y-m-d H:i:s') . "OS") . 
    //                                             '/' . base64_encode($email) . 
    //                                             '/' . md5('Contato-' . $email) 
    //                     ];
               
    //             $contato = (object)[
    //                         'nome' => $chamado->contato->nome,
    //                         'emails' => $emails
    //                     ];
    //         }    
            
            
                    
    //     } catch (Exception $e) {
    //         $ret->msg = $e->getMessage();
    //         return $ret->toJson();
    //     }

    //     //send mail
    //     try {
    //         if($contato){
    //             foreach ($contato->emails as $email) {
                    
    //                 if($email->email<>''){
    //                     $destinatario = (object)[   'nome' => $contato->nome,
    //                                                 'email' => $email->email,
    //                                                 'link' => $email->urlpublico
    //                                             ];
                      
    //                     $job = new ChamadosJob($chamado, $interacao, $destinatario, 'Informativo de chamado');
    //                     dispatch($job);
    //                 }
    //             }
    //         }  
    //         $ret->ok = true;
    //     } catch (Exception $e) {
    //         return $ret->msg = $e->getMessage();
    //     }                      

    //     return $ret->toJson();
    // }   

    // public function sendmailchamado_email(Request $request) {
    //     $ret = new RetornoApiController;
    //     try{
    //         $id = isset($request->id) ? $request->id : 0;
    //         $hash = isset($request->hash) ? $request->hash : '';
    //         $chamado = OrdemServico::select('osordem.id', 'osordem.problemareclamado','osordem.dtabertura', 'osordem.dtfechamento', 
    //                                         'osordem.situacao', 'osordem.idclientecontato', 
    //                                         'osordem.idcliente', 'osordem.tipo', 'osordem.avaliado', 'osordem.idusuariovenda')
    //                     ->where('osordem.id', $id)
    //                     ->first();
    //         $dados = $chamado->toArray();
    //         if(!$chamado) throw new Exception('Chamado não foi encontrado');
    //         $hashcheck = md5($chamado->id . $chamado->dtabertura->format('Y-m-d H:i:s') . 'OS');
    //         if($hashcheck<>$hash) throw new Exception('Hash inválido - ' . $hashcheck  . '- '. $hash);

    //         $interacao = $chamado->interacoespublicas->first();
    //         if($interacao){
    //             $interacao = $interacao->interacao;
    //         }else{
    //             throw new Exception('Nenhum interação no chamado.');
    //         }

    //         // contato
    //         $contato = null;
    //         // se for do operador vai para o cliente
    //         if($interacao->origem=='O'){
    //             if($chamado->contato){
    //                 $emailslist = explode(';', $chamado->contato->email);
    //                 $emails = [];
    //                 foreach ($emailslist as $email) {
    //                     $emails[] = (object)['email' => $email, 
    //                                 'urlpublico' => url('/painelcliente/') . '/chamado/show/publico/' . 
    //                                                     base64_encode($chamado->id) . '/' . 
    //                                                     md5($chamado->id . $chamado->dtabertura->format('Y-m-d H:i:s') . "OS") . 
    //                                                     '/' . base64_encode($email) . 
    //                                                     '/' . md5('Contato-' . $email) 
    //                             ];
    //                 }
    //                 $contato = (object)[
    //                             'nome' => $chamado->contato->nome,
    //                             'emails' => $emails
    //                         ];
    //             }      
    //         }else{
    //             if($chamado->usuario){
    //                 $emails[] = (object)['email' => $chamado->usuario->email, 
    //                                    'urlpublico' => ''
    //                             ];
                    
    //                 $contato = (object)[
    //                             'nome' => $chamado->usuario->nome,
    //                             'emails' => $emails
    //                         ];
    //             }

    //         }
            
                    
    //     } catch (Exception $e) {
    //         $ret->msg = $e->getMessage();
    //         return $ret->toJson();
    //     }

    //     //send mail
    //     try {
    //         if($contato){
    //             foreach ($contato->emails as $email) {
                    
    //                 if($email->email<>''){
    //                     $destinatario = (object)[   'nome' => $contato->nome,
    //                                                 'email' => $email->email,
    //                                                 'link' => $email->urlpublico
    //                                             ];
                      
    //                     $job = new ChamadosJob($chamado, $interacao, $destinatario);
    //                     dispatch($job);
    //                 }
    //             }
    //         }  
    //         $ret->ok = true;
    //     } catch (Exception $e) {
    //         return $ret->msg = $e->getMessage();
    //     }                      

    //     return $ret->toJson();
    // }   

    // public function mensagemuploadanexo(Request $request){
    //     $ret = new RetornoApiController;
    //     try {
    //         $idinteracao = isset($request->idinteracao) ? intval($request->idinteracao) : 0;

    //         $dadostr = (isset($request->dados) ? $request->dados : '');
    //         $dados = (object)json_decode($dadostr);

    //     } catch (Exception $e) {
    //         $ret->ok = false;
    //         $ret->msg = $e->getMessage();
    //         return $ret->toJson();
    //     }

    //     try {
    //         DB::beginTransaction();
        

    //         //get the base-64 from data
    //         $anexobase64 = $dados->base64;
    //         $anexobase64 = substr($anexobase64, strpos($anexobase64, ",")+1);
    //         //decode base64 string
    //         $anexo = base64_decode($anexobase64);   
    //         $md5 = md5($anexo);
    //         $ext = pathinfo($dados->name, PATHINFO_EXTENSION);
    //         $arquivo = InteracaoAnexo::where('anexomd5', $md5)->first();
    //         if(!$arquivo) {
    //             $arquivo = new InteracaoAnexo;
    //             $arquivo->dhcad = Carbon::now();
    //             $arquivo->descricao = $dados->name;
    //             $arquivo->anexo = $anexo;
    //             $arquivo->anexoext = $ext;
    //             $arquivo->anexomd5 = strtoupper($md5);
    //             $ins  = $arquivo->save();
    //             if(!$ins){
    //                 throw new Exception('Falha ao inserir arquivo.' . utf8_decode($ins));
    //             }  
    //         }
            

    //         $arquivolink = new InteracaoAnexoLink;
    //         $arquivolink->idinteracao = $idinteracao;
    //         $arquivolink->idanexo = $arquivo->id;
    //         $ins  = $arquivolink->save();
    //         if(!$ins){
    //             throw new Exception('Falha ao inserir arquivo (link).' . utf8_decode($ins));
    //         }      
    

    //         DB::commit();
    //         $ret->id = $arquivo->id;
    //         $ret->ok = true;
    //     } catch (Exception $e) {
    //         DB::rollBack();
    //         $ret->ok = false;
    //         $ret->msg = $e->getMessage();
    //     }    

    //     return $ret->toJson();    
    // }


    // public function mensagemdownloadanexo(Request $request){
    //     $ret = new RetornoApiController;
    //     try {
    //         $md5 = isset($request->md5) ? $request->md5 : '';
    //         $arquivo = InteracaoAnexo::where('anexomd5', $md5)->first();
    //         if ($arquivo) {
    //           if ($arquivo->storage === 's3') {
    //             $disk = Storage::disk('s3icom');
    //             $filename = $arquivo->fullname;
    //             $internal = $arquivo->fullname;
    //           } else {
    //             $disk = Storage::disk($this->STORAGE_MODO);
    //             $filename = $arquivo->anexomd5 . "." . $arquivo->anexoext;
    //             $internal = '/arquivos/anexoschamados/' . $filename;
    //           }

    //           $exists = $disk->exists($internal);
    //           $url = '';
    //           if ($exists) {
    //             // Storage::disk('public')->put($internal, $arquivo->anexo, 'public');
    //             $url = $disk->temporaryUrl($internal,  now()->addMinutes(1));
    //           }
    //           $ret->ok = true;
    //           $ret->rows = ['url' => $url, 'filename' => $filename];

    //         } else {
    //             $ret->ok = false;
    //             $ret->msg = 'Arquivo não encontrado.';
    //         }
    //     } catch (Exception $e) {
    //         $ret->ok = false;
    //         $ret->msg = $e->getMessage();
    //     }
    //     return $ret->toJson();    
        
        
    // }
   
      
    // public function contadoresGroupCliente(Request $request) {

    //     $ret = new RetornoApiController;
    //     try{
    //         $cnpjs = isset($request->cnpjs) ? $request->cnpjs : '';
    //         $cnpjLista = explode(',', $cnpjs);
    //         if ($cnpjs=='') throw new Exception('Nenhum CNPJ informado');
    //         if (count($cnpjLista)<=0) throw new Exception('Nenhum CNPJ informado');            
            
    //         $idrelacionamento = isset($request->idrelacionamento) ? intval($request->idrelacionamento) : 0;
            
    //         $contato = Contato::find($idrelacionamento);
    //         if(!$contato) throw new Exception("Contato de relacionamento não encontrado");
    //         if(!$contato->lixeira==0) throw new Exception("Contato de relacionamento inativado.");

    //         $cnpjJoin = "'" . implode("','", $cnpjLista)."'";
    //         $clientes = Clientes::where('ativo', 1)
    //                             ->whereRaw('((cnpj in (' . $cnpjJoin . ')) or (concat("000",cpf) in (' . $cnpjJoin . ')))')
    //                             ->get();    
    //         // dd($clientes)        ;
            
            
    //         //40000375, "Permite ver chamado com valores(R$)"
    //         // $permitevervalores = $contato->checkpermite($idcliente, '40000375') ? 1 : 0;
    //         //40000374, "Permite ver chamado de todos os usuários da empresa
    //         // $permitevertodos = $contato->checkpermite($idcliente, '40000374');
           
    //         // meus chamados
    //         $dados = [];
    //         $emabertosCount = 0;
    //         $emabertos = [];
    //         $pendenteRespEmAbertoCount = 0;
    //         $pendenteRespEmAberto = [];
    //         $naolidoCount = 0;
    //         $naolido = [];            
    //         foreach ($clientes as $key => $cliente) {

    //             // em abertos
    //             $OSs = [];
    //             $OSLista = OrdemServico::where('osordem.idclientecontato', $contato->id)
    //                         ->where('osordem.idcliente', $cliente->codcliente)
    //                         ->where('osordem.modo', 'OS')
    //                         ->where('osordem.situacao', 'Aberta')
    //                         ->get();
    //             foreach ($OSLista as $key => $OS) {
    //                 $OSs[] = [  'id' => $OS->id,
    //                             'descricao' => $OS->problemareclamado,
    //                             'situacao' => $OS->situacao

    //                 ];
    //             }
    //             $emabertos[] = ["cnpj" => ($cliente->pessoa=='F' ? $cliente->cpf : $cliente->cnpj),
    //                                     'ticket' => $OSs];
    //             $emabertosCount = $emabertosCount  + $OSLista->count();       
                                            
   
                

    //             // pendente resposta cliente
    //             $OSs = [];
    //             $OSLista = OrdemServico::leftJoin('osfases', 'osordem.idfase', '=', 'osfases.id')
    //                                         ->where('osordem.idclientecontato',  $contato->id)
    //                                         ->where('osordem.idcliente', $cliente->codcliente)
    //                                         ->where('osordem.modo', 'OS')
    //                                         ->where('osordem.situacao', 'Aberta')
    //                                         ->where('osfases.pendentecliente', 1)
    //                                         ->get();    
                                            
    //             foreach ($OSLista as $key => $OS) {
    //                 $OSs[] = [  'id' => $OS->id,
    //                             'descricao' => $OS->problemareclamado,
    //                             'situacao' => $OS->situacao

    //                 ];
    //             }
    //             $pendenteRespEmAberto[] = ["cnpj" => ($cliente->pessoa=='F' ? $cliente->cpf : $cliente->cnpj),
    //                                     'ticket' => $OSs];
    //             $pendenteRespEmAbertoCount = $pendenteRespEmAbertoCount  + $OSLista->count();                                               
   
                
                
    //             // nao lidos
    //             $contatoid=$contato->id;
    //             $OSs = [];
    //             $OSLista = OrdemServico::select('osordem.id', 'osordem.problemareclamado', 'osordem.situacao',  DB::raw('max(osleitura.dataleitura) as d, max(interacao.datahora) as m'))
    //                             ->leftJoin('interacaoos', 'osordem.id', '=', 'interacaoos.idos')
    //                             ->leftJoin('interacao', function($join)  {
    //                                         $join->on('interacaoos.idinteracao', '=', 'interacao.id')
    //                                         ->where('interacao.origem', '<>', 'C')
    //                                         ->where('interacao.restrito', '=', 0);
    //                                     })
    //                             ->leftJoin('osleitura', function($join) use ($contatoid)  {
    //                                 $join->on('osordem.id', '=', 'osleitura.idos')
    //                                     ->where('osleitura.idcontato', $contatoid);
    //                             })                                        
                                
    //                     ->where('osordem.idcliente', $cliente->codcliente)
    //                     ->where('osordem.idclientecontato', $contato->id)
    //                     ->where('osordem.modo', 'OS')
    //                     ->whereRaw('if(osordem.situacao="Aberta", true, datediff(now(), dtFechamento)<=7  )')
    //                     ->orderBy('osordem.dtabertura', 'desc')
    //                     ->groupBy('osordem.id')
    //                     ->havingRaw('if(max(osleitura.dataleitura) is null, true, max(osleitura.dataleitura) < max(interacao.datahora))')
    //                     ->get();
    //             foreach ($OSLista as $key => $OS) {
    //                 $OSs[] = [  'id' => $OS->id,
    //                             'descricao' => TrataJson( $OS->problemareclamado),
    //                             'situacao' => $OS->situacao

    //                 ];
    //             }
    //             $naolido[] = ["cnpj" => ($cliente->pessoa=='F' ? $cliente->cpf : $cliente->cnpj),
    //                                     'ticket' => $OSs];
    //             $naolidoCount = $naolidoCount  + $OSLista->count();                           
                                        
    //         }


    //         $dados = [  "emabertos" => ["itens" => $emabertos, "total" => $emabertosCount],
    //                     "pendenterespaberto" => ["itens" => $pendenteRespEmAberto, "total" => $pendenteRespEmAbertoCount],
    //                     "naolido" => ["itens" => $naolido, "totalticket" => $naolidoCount],
    //                 ];


    //         $ret->ok = true;
    //         $ret->rows = $dados;

    //     } catch (Exception $e) {
    //         $ret->msg = $e->getMessage();
    //     }

    //     return $ret->toJson();
    // }     

    // public function ultimaatualizacao(Request $request) {

    //     $ret = new RetornoApiController;
    //     try{
    //         $cnpjs = isset($request->cnpjs) ? $request->cnpjs : '';
    //         $cnpjLista = explode(',', $cnpjs);
    //         if ($cnpjs=='') throw new Exception('Nenhum CNPJ informado');
    //         if (count($cnpjLista)<=0) throw new Exception('Nenhum CNPJ informado');            
            
    //         $idrelacionamento = isset($request->idrelacionamento) ? intval($request->idrelacionamento) : 0;
            
    //         $contato = Contato::find($idrelacionamento);
    //         if(!$contato) throw new Exception("Contato de relacionamento não encontrado");
    //         if(!$contato->lixeira==0) throw new Exception("Contato de relacionamento inativado.");

    //         $cnpjJoin = "'" . implode("','", $cnpjLista)."'";
    //         $clientes = Clientes::where('ativo', 1)
    //                             ->whereRaw('((cnpj in (' . $cnpjJoin . ')) or (concat("000",cpf) in (' . $cnpjJoin . ')))')
    //                             ->get();    
            
                               
    //         $dados = [];
    //         $ultimaatualizacao = null;   
    //         if($contato->i12ultimaatualizacao) $ultimaatualizacao = $contato->i12ultimaatualizacao;

    //         foreach ($clientes as $key => $cliente) {
    //             if($cliente->i12ultimaatualizacao) {
    //                 if (($cliente->i12ultimaatualizacao > $ultimaatualizacao) OR (!$ultimaatualizacao)){
    //                     $ultimaatualizacao = $cliente->i12ultimaatualizacao;
    //                 }
    //             }                      
    //         }

    //         if (!($ultimaatualizacao==null)) $ultimaatualizacao = $ultimaatualizacao->format('Y-m-d H:i:s');
    //         $dados = [  "ultimaatualizacao" => $ultimaatualizacao ];

            
    //         $ret->ok = true;
    //         $ret->rows = $dados;

    //     } catch (Exception $e) {
    //         $ret->msg = $e->getMessage();
    //     }
        
        
    //     try{
    //         DB::beginTransaction();

    //         foreach ($clientes as $key => $cliente) {
    //             $relacao = ContatoCliente::where('idcontato', $contato->id)->where('idcliente', $cliente->codcliente)->first();
    //             if($relacao){
    //                 $relacao->ultimosynci12  = Carbon::now();               
    //                 $saved = $relacao->save();
    //                 if (!$saved)
    //                     throw new Exception('Falha ao salvar relacionamento!'); 
    //             }
                            
    //         }
            
    //         DB::commit();
    //         $ret->ok = true;
    //     } catch (Exception $e) {

    //         DB::rollBack();
    //         $ret->ok = false;
    //         $ret->msg = $e->getMessage();
    //     }                   

    //     return $ret->toJson();
    // }         
}
