<?php

namespace App\Console\Commands\adm;

use Illuminate\Console\Command;

use App\Jobs\adm\BackupReportAlertJob;

class BackupReportAlert extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:reportalert';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envia relatório de aviso e alertas de backup';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
      $job = (new BackupReportAlertJob());
      dispatch($job);
    }
}
