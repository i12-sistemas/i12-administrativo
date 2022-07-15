<?php

namespace App\Console\Commands\adm;

use Illuminate\Console\Command;
use Exception;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;


class BackupInventory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:get';
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

   
    public function handle()
    {
      $cc = app()->make('App\Http\Controllers\API\v1\admin\BackupController');

      $diskatual = 's3backup_1';

      $disk=Storage::disk($diskatual);
      $d = $disk->directories('backup-mysql');
      foreach ($d as $key => $dir) {
        $aDir = explode('/', $dir);
        $cnpjDir = $aDir[count($aDir)-1];
        $this->info('Processando ' . $cnpjDir  .' - disco ' . $diskatual . ' ...');
        $ret = app()->call([$cc, 'processa'], ['cnpj' => $cnpjDir, 's3disk' => $diskatual, 'forceclear' => true]);
        $this->info($ret->ok ? "OK" : "Falhou - "  . $ret->msg);
      }

      $diskatual = 's3backup';

      $disk=Storage::disk($diskatual);
      $d = $disk->directories('backup-mysql');
      foreach ($d as $key => $dir) {
        $aDir = explode('/', $dir);
        $cnpjDir = $aDir[count($aDir)-1];
        $this->info('Processando ' . $cnpjDir  .' - disco ' . $diskatual . ' ...');
        $ret = app()->call([$cc, 'processa'], ['cnpj' => $cnpjDir, 's3disk' => $diskatual, 'forceclear' => false]);
        $this->info($ret->ok ? "OK" : "Falhou - "  . $ret->msg);
      }      
      
    }
}
