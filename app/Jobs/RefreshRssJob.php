<?php

namespace App\Jobs;

use App\Episode;
use App\Podcast;
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
                $newEpisode = new Episode();
                $newEpisode->podcast_id = $this->podcast->id;
                $newEpisode->title = $episode->title;
                $newEpisode->name = $episode->title;
                $newEpisode->description = $episode->metadata->description;
                $newEpisode->published_at = $episode->metadata->pubDate;
                $newEpisode->library_id = $this->podcast->library_id;
                $newEpisode->directory_id = $this->podcast->directory_id;
                $newEpisode->metadata_set = true;
                $newEpisode->podcast_id = $this->podcast->id;
                $newEpisode->downloaded = false;
                $newEpisode->download_url = $episode->enclosure->url;
                $newEpisode->guid = $episode->guid;
                $newEpisode->path='';
                $newEpisode->save();
            }

        }

    }

}
