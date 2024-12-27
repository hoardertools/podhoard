<?php

namespace App\Console\Commands;

use App\Jobs\RefreshRssJob;
use App\Managers\PodcastManager;
use App\Podcast;
use Illuminate\Console\Command;

class RescanRssFeeds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:rescan-rss-feeds';

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
        foreach(Podcast::whereNotNull('rss_feed')->get() as $podcast){

            RefreshRssJob::dispatch($podcast);

        }
    }
}
