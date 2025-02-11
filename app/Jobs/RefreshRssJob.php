<?php

namespace App\Jobs;

use App\Download;
use App\Episode;
use App\Log;
use App\Podcast;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use PhanAn\Poddle\Poddle;

class RefreshRssJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private Podcast $podcast)
    {
    }

    public function handle(): void
    {

        $poddle = Poddle::fromUrl($this->podcast->rssUrl);

        foreach($poddle->getEpisodes() as $episode){

            if(!Episode::where("podcast_id", "=", $this->podcast->id)->where("guid", "=", $episode->guid)->exists()){
                if(!Download::where("podcast_id", "=", $this->podcast->id)->where("guid", "=", $episode->guid)->exists()){
                    $download = new Download();
                    $download->title = $episode->title;
                    $download->name = $episode->title;
                    $download->description = $episode->metadata->description;
                    $download->published_at = $episode->metadata->pubDate;
                    $download->library_id = $this->podcast->library_id;
                    $download->directory_id = $this->podcast->directory_id;
                    $download->podcast_id = $this->podcast->id;
                    $download->downloaded = false;
                    $download->downloaded_at = Carbon::parse('1990-01-01 00:00:00');
                    $download->download_url = $episode->enclosure->url;
                    $download->guid = $episode->guid;
                    $download->path='';
                    $download->duration = 0;
                    $download->filesize = 0;

                    Log::log("New episode found: " . $download->title . " for podcast " . $this->podcast->title, "RSS Rescan", "info");
                    $download->save();
                }

            }

        }

        $this->podcast->last_rss_scanned_at = now();
        $this->podcast->save();

    }

}
