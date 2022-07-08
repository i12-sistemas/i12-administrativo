<?php

namespace App\Http\Controllers\api\v2\painelcliente;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Controllers\RetornoApiController;
use Exception;
use App\models\OrdemServico;
use Illuminate\Support\Facades\DB;
use App\models\Interacao;
use App\models\Clientes;
use App\models\Contato;
use App\models\InteracaoOS;
use App\models\InteracaoCliente;
use Carbon\Carbon;
use App\models\OSLeitura;
use App\models\AvaliacaoPergunta;
use App\models\AvaliacaoRespOS;
use App\models\Empresa;
use App\models\InteracaoAnexoLink;
use App\models\InteracaoAnexo;
use Illuminate\Support\Facades\Storage;
use App\Jobs\painelcliente\ChamadosJob;
use App\models\OSLocalAtendimento;
use App\models\Configuracao;
use App\models\User;
use App\models\ContatoCliente;

class TicketController extends Controller
{

  private $STORAGE_MODO;

  public function __construct()
  {
    $this->STORAGE_MODO = env('STORAGE_MODO', 'public');
  }

  public function find  (Request $request, $id) {
    $ret = new RetornoApiController;
    try{
      $id = isset($request->id) ? $request->id : 0;
      $hash = isset($request->hash) ? $request->hash : '';
      if($hash ? $hash === '' : true) throw new Exception('Hash de acesso inválido');



      $chamado = OrdemServico::select('osordem.id', 'osordem.problemareclamado','osordem.dtabertura', 'osordem.dtfechamento', 
                                      'osordem.idfase', 'osordem.situacao', 'osordem.idclientecontato', 
                                      'osordem.idcliente', 'osordem.tipo', 'osordem.avaliado')
                  ->where('osordem.id', $id)
                  ->first();

      $dados = $chamado->toArray();
      $dados['dtabertura'] = $chamado->dtabertura->format('Y-m-d H:i:s');
      $dados['dtabertura'] = $chamado->dtfechamento ? $chamado->dtabertura->format('Y-m-d H:i:s') : null;
      
      if(!$chamado) throw new Exception('Chamado não foi encontrado');

      $hashcheck = md5($chamado->id . $chamado->dtabertura->format('Y-m-d H:i:s') . 'OS');
      if($hashcheck<>$hash) throw new Exception('Hash inválido - ' . $hashcheck  . '- '. $hash);

      $fase = [];
      if($chamado->fase){
          $fase = [   'id' => $chamado->fase->id,
                      'cor' => $chamado->fase->cor,
                      'corfonte' => $chamado->fase->corfonte,
                      'descricaocliente' => $chamado->fase->descricaocliente,
                      'pendentecliente' => $chamado->fase->pendentecliente
                  ];
      } 

        $cliente = [];
        if($chamado->cliente){
           
            $cliente = ['id' => $chamado->cliente->codcliente,
                        'nome' => $chamado->cliente->nome,
                        'fantasia' => $chamado->cliente->fantasia,
                        'cnpj' => $chamado->cliente->cnpj,
                    ];
        }   
     
        // contato
        $contato = [];
        if($chamado->contato){
            $emailslist = explode(';', $chamado->contato->email);
            $emails =[];
            foreach ($emailslist as $email) {
                $emails[] = ['email' => $email, 
                             'urlpublico' => url('/painelcliente/') . '/chamado/show/publico/' . 
                                                 base64_encode($chamado->id) . '/' . 
                                                 md5($chamado->id . $chamado->dtabertura->format('Y-m-d H:i:s') . "OS") . 
                                                 '/' . base64_encode($email) . 
                                                 '/' . md5('Contato-' . $email) 
                        ];
            }
            $contato = ['id' => $chamado->contato->id,
                        'nome' => $chamado->contato->nome,
                        'emails' => $emails
                    ];
        }                
    
           

        //interações
        $interacoes = Interacao::select('interacao.id', 'interacao.texto', 'interacao.origem', 'interacao.ip', 'interacao.datahora', 'interacao.idcontatoacao')
                            ->join('interacaoos', 'interacao.id', '=', 'interacaoos.idinteracao')
                            ->where('interacao.excluido', 0)
                            ->where('interacaoos.idos', $chamado->id)
                            ->where('interacao.restrito', 0)
                            ->orderby('interacao.datahora', 'desc')
                            ->get();

        $interacoesdata = [];
        foreach ($interacoes as $interacao) {
            $anexos = [];
            foreach ($interacao->anexos as $anexo) {
                $anexos[] = [  'descricao'   => utf8_encode($anexo->descricao),
                               'url' => url('/painelcliente/arquivos/') . '/'.  $anexo->anexomd5,
                               'md5' => $anexo->anexomd5
                               
                    ];
            }
                
            $interacoesdata[] = [   'id' => $interacao->id,
                        'texto' => $interacao->texto,
                        'origem' => $interacao->origem,
                        'ip' => $interacao->ip,
                        'datahora' => $interacao->datahora->format('Y-m-d H:i:s'),
                        'contatoacao' => ['id' => $interacao->contatoacao->id, 'nome' => $interacao->contatoacao->nome],
                        'anexos' => $anexos
                    ];
            
        } 
          
        
       //ultima leitura1
        $ultimaleitura = OSLeitura::where('idos', $chamado->id)
                            ->orderby('dataleitura', 'desc')
                            ->first();
        
        $dados["fase"] = $fase;
        $dados["cliente"] = $cliente;
        $dados["contato"] = $contato;
        $dados["interacoes"] = $interacoesdata;
        $dados["ultimaleitura"] = null;
        if($ultimaleitura) $dados["ultimaleitura"] = $ultimaleitura->dataleitura->format('Y-m-d H:i:s');
        

        // dd($dados);
        $ret->ok = true;
        $ret->rows = $dados;
    } catch (Exception $e) {
        $ret->msg = $e->getMessage();
    }
    // dd($ret);
    return $ret->toJson();
}    

}
