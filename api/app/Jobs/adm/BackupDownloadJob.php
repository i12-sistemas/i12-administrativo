<?php

namespace App\Jobs\adm;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Mail\adm\BackupDownloadMail;

class BackupDownloadJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $arquivo;
    private $usuario;

    public $tries = 3;
    public $maxExceptions = 3;
    public $timeout = 120;

    public function __construct($arquivo, $usuario)
    {
      $this->arquivo = $arquivo;
      $this->usuario = $usuario;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
      \Mail::to(env('MAIL_SUPORTE', ''))->send(new BackupDownloadMail($this->arquivo, $this->usuario));
    }
}
