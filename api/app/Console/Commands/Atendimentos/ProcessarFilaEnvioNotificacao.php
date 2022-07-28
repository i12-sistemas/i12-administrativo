<?php

namespace App\Console\Commands\Atendimentos;

use Illuminate\Console\Command;
use Illuminate\Http\Request;

use Exception;
use Illuminate\Support\Facades\DB;
use App\models\Interacao;

class ProcessarFilaEnvioNotificacao extends Command
{
  protected $signature = 'atendimento:processarfilaenvionotificacao';
  protected $description = 'Processa a fila de interação pra envio de WhatsApp e E-mail';

  public function __construct()
  {
      parent::__construct();
  }

  public function handle(Request $request)
  {
    try {
      $continua = true;
      $erroconsecutivo = 5;
      $pagesize = 50;
      $limitetentativas = 5;

      $qry = Interacao::select(DB::raw('count(distinct interacaoos.idos) as qtdeos, interacaoos.idos'))
              ->join('interacaoos', 'interacao.id', 'interacaoos.idinteracao')
              ->where('interacao.sendmail', '=', 1)
              ->where('interacao.excluido', '=', 0)
              ->where('interacao.restrito', '<>', 1)
              ->where('interacao.emailstatus', '=', 0)
            ->groupBy('interacaoos.idos')
            ->orderBy('interacao.datacad', 'asc')
            ;

      $datasetCount = $qry->get();
      $recordtotal = $datasetCount ? count($datasetCount) : 0;
      $this->warn('Total de interações-e pendentes: ' . $recordtotal);

      $dataset = $qry->paginate($pagesize);

      while ($recordtotal > 0) {

      //       $ctes = $qry->paginate($pagesize);

      //       if (!$ctes) {
      //           $this->warn('Nenhum CT-e pendente');
      //           $continua = false;
      //           throw new Exception('');
      //       }
      //       if (count($ctes) <= 0) {
      //           $this->warn('Nenhum CT-e pendente');
      //           $continua = false;
      //           throw new Exception('');
      //       }
      //       $i = 1;
      //       $recordcount = $ctes->count();
          $request->query->add(['rawreturn' => 1]);
          foreach ($dataset as $key => $row) {
              $this->info(($key+1) . '/' . count($dataset) . ' de ' . $recordtotal . ' - Lendo O.S. ' . $row->idos);

              $cc = app()->make('App\Http\Controllers\API\v1\icomservices\painelcliente\AtendimentoController');
              $ret = app()->call([$cc, 'enviaNotificacaoAtendimento'], ['numero' => $row->idos ]);
              if ($ret->ok) {
                  $this->info($ret->msg);
                  $continua = true;
              } else {
                  $this->error('Erro ao processar :: ' . $ret->msg);
              }
              // $i = $i + 1;
              // sleep(0.1);
              $this->info('');
          }
      //       $this->info('Lote finalizando! Checando se existe mais CT-e para processar...');
          sleep(2);
          $datasetCount = $qry->get();
          $recordtotal = $datasetCount ? count($datasetCount) : 0;
          $this->warn('Total de O.S. pendentes: ' . $recordtotal);
          $continua = ($recordtotal > 0 );
        }


        // $this->info('Relacionando cargas...');
        // $cc = app()->make('App\Http\Controllers\api\v1\CargaEntregaController');
        // $retRelacionamento = app()->call([$cc, 'relacionarMDFeComCargas'], []);
        // if ((!$retRelacionamento->ok) && ($retRelacionamento->msg !== ''))  $this->warn('Relacionamento de cargas: ' . $retRelacionamento->msg);

    } catch (\Throwable $th) {
      if ($th->getMessage() !== '') {
        $this->error('Processo abortado por erro:');
        $this->error($th->getMessage());
      }
    }
    $this->info('Processamento finalizado!');
  }
}
