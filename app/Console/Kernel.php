<?php

namespace App\Console;

use App\Console\Commands\RescanLibrary;
use App\Console\Commands\RescanRssFeeds;
use App\Console\Commands\StartDownload;
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
        RescanLibrary::class,
        RescanRssFeeds::class,
        StartDownload::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
         $schedule->command('app:rescan-library')->daily();
         $schedule->command('app:rescan-rss-feeds')->everySixHours();
        $schedule->command('app:start-download')->everyMinute();
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
