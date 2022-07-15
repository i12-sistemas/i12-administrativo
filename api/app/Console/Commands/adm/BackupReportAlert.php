<?php

namespace App\Console\Commands\adm;

use Illuminate\Console\Command;

use App\Jobs\adm\BackupReportAlertJob;

class BackupReportAlert extends Command
{
    protected $signature = 'backup:reportalert';
    protected $description = 'Envia relatório de aviso e alertas de backup';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
      $job = (new BackupReportAlertJob());
      dispatch($job);
    }
}
