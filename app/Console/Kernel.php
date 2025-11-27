<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\SyncRevSales::class,
        Commands\SyncRevInventory::class,
        Commands\TestErpRevConnection::class,
        Commands\RegisterBookInErprev::class,
        Commands\TestBookRegistration::class,
        Commands\UpdateRevSyncLogsEnum::class,
        Commands\TestErpRevData::class,
        Commands\ShowBookReviewLogs::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Schedule sales sync every hour
        $schedule->command('rev:sync-sales')->hourly();
        
        // Schedule inventory sync daily at 2 AM
        $schedule->command('rev:sync-inventory')->dailyAt('02:00');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}