<?php

namespace App\Mail\adm;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use App\models\I12BackupfileS3;
use App\models\Clientes;

class BackupReportAlertMail extends Mailable
{
  use Queueable, SerializesModels;

  protected $dados;
  protected $gratuito;
  protected $indicegratuito;
  protected $pago;
  protected $indicepago;

  public function __construct()
  {
    $this->dados = Clientes::select('clientes.codcliente', 'clientes.nome', 'clientes.fantasia', 'clientes.cidade',
              'clientes.i12controlabkp', 'clientes.pessoa', 'clientes.cpf', 'clientes.cnpj',
              DB::RAW('max(i12_backupfiles3.lastmodified) as lastmodified'), 
              DB::RAW('ifnull(max(i12_backupfiles3.size),0) as totalsize'),
              DB::RAW('count(distinct i12_backupfiles3.id) as qtdearquivos'),
              DB::RAW('TIMESTAMPDIFF(HOUR, max(i12_backupfiles3.lastmodified), NOW()) as horasatras')
              )
              ->leftJoin('i12_backupfiles3', DB::RAW('if(clientes.pessoa="F",concat("000",clientes.cpf),clientes.cnpj)'), '=', 'i12_backupfiles3.cnpj' )
              ->where('clientes.ativo', '=', 1)
              ->whereIn('clientes.i12controlabkp', [1,2])
              ->whereRaw('coalesce(if(clientes.pessoa="F",clientes.cpf,clientes.cnpj),"")<>""')
              ->groupBy('clientes.codcliente')
              ->havingRaw('(horasatras > ?) or (horasatras is null)', [72])
              ->orderBy(DB::RAW('if(lastmodified, 1, 0)'))
              ->orderBy('lastmodified')
              ->get();

    $sucesso = Clientes::select('clientes.codcliente', 
              DB::RAW('ifnull(max(i12_backupfiles3.size),0) as totalsize'),
              DB::RAW('count(distinct i12_backupfiles3.id) as qtdearquivos')
              )
              ->leftJoin('i12_backupfiles3', DB::RAW('if(clientes.pessoa="F",concat("000",clientes.cpf),clientes.cnpj)'), '=', 'i12_backupfiles3.cnpj' )
              ->where('clientes.ativo', '=', 1)
              ->whereIn('clientes.i12controlabkp', [1,2])
              ->whereRaw('coalesce(if(clientes.pessoa="F",clientes.cpf,clientes.cnpj),"")<>""')
              ->groupBy('clientes.codcliente')
              ->get();              

      $qtdegratuito = $this->dados->sum(function ($item) {
        if ($item->i12controlabkp == 1) 
          return 1;
      });

      $qtdepago = $this->dados->sum(function ($item) {
        if ($item->i12controlabkp == 2) 
          return 1;
      });

      $qtdetotal = $sucesso->count();

      if ($qtdetotal!=0) {
        $this->indicegratuito = round((($qtdegratuito / $qtdetotal)*100),2);
        $this->indicepago = round((($qtdepago / $qtdetotal)*100),2);
      } else {
        $this->indicegratuito = 0;
        $this->indicepago = 0;
      }

      $this->pago = $qtdepago;
      $this->gratuito = $qtdegratuito;
  }

  public function build()
  {

    return $this->subject('BACKUP - ' . ($this->indicegratuito + $this->indicepago)  . '%  dos backups em alerta | ' . ($this->gratuito + $this->pago) . ' cliente(s)' )
        ->markdown('adm.mail.backupreportalert')
        ->with([
            'dados' => $this->dados,
            'gratuito' => $this->gratuito,
            'indicegratuito' => $this->indicegratuito,
            'pago' => $this->pago,
            'indicepago' => $this->indicepago,
        ]);
  }
}
