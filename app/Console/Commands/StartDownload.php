<?php

namespace App\Console\Commands;

use App\Jobs\DownloadEpisodeJob;
use Illuminate\Console\Command;

class StartDownload extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:start-download';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        DownloadEpisodeJob::dispatch()->onQueue("downloads");
    }
}
