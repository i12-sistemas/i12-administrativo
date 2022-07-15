<?php

namespace App\Mail\adm;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BackupDownloadMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $arquivo;
    protected $usuario;

    public function __construct($arquivo, $usuario)
    {
      $this->arquivo = $arquivo;
      $this->usuario = $usuario;
    }

    public function build()
    {
      $info = (object)[
        'dhacao' => Carbon::now(),
        'ip' => \Request::getClientIp(true),
      ];
      return $this->subject('INFO - Registro de download de backup - ' . $this->arquivo->shortfilename)
          ->markdown('adm.mail.backupdownloaded')
          ->with([
              'arquivo' => $this->arquivo,
              'usuario' => $this->usuario,
              'info' => $info,
          ]);
    }
}
