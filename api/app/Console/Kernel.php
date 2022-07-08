<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
      '\App\Console\Commands\adm\BackupInventory',
    ];

    protected function schedule(Schedule $schedule)
    {
      //Run the task daily at 1:00 & 13:00
      $schedule->command('backup:get')->twiceDaily(12, 23);
      $schedule->command('backup:reportalert')->daily(23);
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}
