<?php

namespace App\Jobs\adm;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use App\models\I12BackupfileS3;
use Exception;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\adm\BackupDeleteMail;

class BackupDeleteJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $dados;
    private $usuario;
    private $ip;

    public $tries = 3;
    public $maxExceptions = 3;
    public $timeout = 120;

    public function __construct($dados, $usuario, $ip)
    {
      $this->dados = $dados;
      $this->usuario = $usuario;
      $this->ip = $ip;
    }

    public function handle()
    {
      try {
        $disk_0 = Storage::disk('s3backup');
        $disk_1 = Storage::disk('s3backup_1');

        $listsDisk_0 = [];
        $listsDisk_1 = [];
        foreach ($this->dados as $arquivo) {
          if ($arquivo->bucket == 0) {
            if ($disk_0->exists($arquivo->filename))
              $listsDisk_0[] = $arquivo->filename;
          } else {
            if ($disk_1->exists($arquivo->filename))
              $listsDisk_1[] = $arquivo->filename;
          }
        }

        $disk_0->delete($listsDisk_0);
        $disk_1->delete($listsDisk_1);

        try{
          DB::beginTransaction();

          $lisdeleted = [];
          foreach ($this->dados as $arquivo) {
            $lisdeleted[] = $arquivo;
            $del = I12BackupfileS3::where('md5', $arquivo->md5)->delete();
          }        
          
          DB::commit();
        } catch (Exception $e) {
          DB::rollBack();
          throw new Exception($e->getMessage());
        }

        \Mail::to(env('MAIL_SUPORTE', ''))->send(new BackupDeleteMail($lisdeleted, $this->usuario, $this->ip));

      } catch (Exception $e) {
        throw new Exception($e->getMessage());
      }
    }
}
