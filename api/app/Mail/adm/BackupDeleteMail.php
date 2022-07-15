<?php

namespace App\Mail\adm;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BackupDeleteMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $arquivos;
    protected $usuario;
    protected $ip;

    public function __construct($arquivos, $usuario, $ip)
    {
      $this->arquivos = $arquivos;
      $this->usuario = $usuario;
      $this->ip = $ip;

    }

    public function build()
    {
      $size = 0;
      foreach ($this->arquivos as $arquivo) {
        $size = $size + $arquivo->size;
      }
      $info = (object)[
        'dhacao' => Carbon::now(),
        'ip' => $this->ip,
        'qtde' => count($this->arquivos),
        'size' => $size
      ];
      return $this->subject('INFO - Registro de backup excluído')
          ->markdown('adm.mail.backupdeleted')
          ->with([
              'arquivos' => $this->arquivos,
              'usuario' => $this->usuario,
              'info' => $info,
          ]);
    }
}
