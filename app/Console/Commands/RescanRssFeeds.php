<?php

namespace App\Console\Commands;

use App\Jobs\RefreshRssJob;
use App\Log;
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
        foreach(Podcast::whereNotNull('rssUrl')->get() as $podcast){
            Log::log("Rescanning RSS feed: $podcast->name", "RSS Feed Rescan", "info" );

            RefreshRssJob::dispatch($podcast);

        }
    }
}
