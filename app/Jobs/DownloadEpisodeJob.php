<?php

namespace App\Jobs;

use App\Directory;
use App\Download;
use App\Episode;
use App\Managers\EpisodeManager;
use App\Managers\PodcastManager;
use App\Podcast;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Psy\Util\Str;

class DownloadEpisodeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
    }

    public function handle(): void
    {
        if(Download::orderBy("created_at", "ASC")->exists()){

            $episode = Download::orderBy("created_at", "ASC")->first();

            try{
                $download = file_get_contents($episode->download_url);
            }catch (\Exception $e){
                unset($e);
                \Log::error("Failed to download episode: " . $episode->id . PHP_EOL . "Error: " . $e->getMessage());
                $episode->delete();
                DownloadEpisodeJob::dispatch();
                exit(-1);
            }

            $podcast = Podcast::findOrFail($episode->podcast_id);

            if(!\Storage::disk('base')->exists($podcast->path)){
                \Storage::disk('base')->makeDirectory($podcast->path);
                chmod($podcast->path , 0755);
            }

            if(\Storage::disk('base')->put($podcast->path . "/" . \Illuminate\Support\Str::slug($episode->title) . "." . $episode->id . ".mp3", $download)){
                $episode->downloaded = true;
                $episode->path = $podcast->path . "/" . \Illuminate\Support\Str::slug($episode->title) . "." . $episode->id . ".mp3";
                chmod($episode->path , 0755);


                $newEpisode = new Episode();
                $newEpisode->title = $episode->title;
                $newEpisode->description = $episode->description;
                $newEpisode->podcast_id = $episode->podcast_id;
                $newEpisode->library_id = $episode->library_id;
                $newEpisode->directory_id = $episode->directory_id;
                $newEpisode->path = $episode->path;
                $newEpisode->name = $episode->name;
                $newEpisode->published_at = $episode->published_at;
                $newEpisode->metadata_set = false;
                $newEpisode->guid = $episode->guid;
                $newEpisode->save();

                $meta = new EpisodeManager($newEpisode);
                $meta->setMetaData();

                $podcastManager = new PodcastManager($podcast);
                $podcastManager->refresh();

                $episode->delete();
            }



            DownloadEpisodeJob::dispatch()->onQueue("downloads");

        }
    }
}
