<?php

namespace App\Jobs\adm;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Mail\adm\BackupReportAlertMail;

class BackupReportAlertJob implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  public $tries = 1;
  public $maxExceptions = 1;
  public $timeout = 20;

  public function __construct()
  {
  }

  public function handle()
  {
    \Mail::to(env('MAIL_SUPORTE', ''))->send(new BackupReportAlertMail());
  }
}
