<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Events\Dispatcher;
use JeroenNoten\LaravelAdminLte\Events\BuildingMenu;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Queue;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;

use Exception;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\Jobs\painelcliente\InteracaoAddClienteJob;

class AppServiceProvider extends ServiceProvider
{
    public function boot(Dispatcher $events)
    {

      Queue::before(function (JobProcessing $event) {
      });

      Queue::after(function (JobProcessed $event) {
        \Log::debug("JobProcessed");
        $payload = json_decode($event->job->getRawBody());
        $obj = unserialize($payload->data->command);
        $interacao = $obj->interacao;
        try{
          DB::beginTransaction();
          $interacao->emaillist = $obj->email;
          $interacao->emailstatus = 1;
          $interacao->emailerror = '';
          $interacao->emaildhprocessado = Carbon::now();
          $interacao->save();
          DB::commit();
        } catch (Exception $e) {
          DB::rollBack();
          \Log::error('JobProcessed: ' . $e->getMessage());
        }          
      });
            
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
      setLocale(LC_TIME, 'pt_BR');
      \Carbon\Carbon::setLocale('pt_BR');
      foreach (glob(app_path() . '/Helpers/*.php') as $file) {
          require_once($file);
      }
    }
}
