<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Events\Dispatcher;
use JeroenNoten\LaravelAdminLte\Events\BuildingMenu;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AppServiceProvider extends ServiceProvider
{
    public function boot(Dispatcher $events)
    {
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
