<?php

namespace App\Jobs;

use App\Episode;
use App\Library;
use App\Managers\EpisodeManager;
use App\Managers\MetadataManager;
use App\Managers\PodcastManager;
use App\Metadata;
use App\Podcast;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SetInitialMetaDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private readonly Library $library)
    {
    }

    public function handle(): void
    {

        $metaData = [];
        $episodes = [];

        foreach($this->library->episodes()->where("metadata_set", "=", false)->take(1000)->get() as $episode) {

            $metadataManager = new MetadataManager($episode);
            $metaObject = $metadataManager->setDbMetaData(true);
            foreach($metaObject->metaData as $metadata) {
                $metaData[] = $metadata->toArray();
            }

            $episodes[] = $metaObject->episode->toArray();

        }

        $chuckedMetadata = array_chunk($metaData,10000,true);

        foreach ($chuckedMetadata as $chuck)
        {
            Metadata::insert($chuck);
        }

        $chuckedEpisodes = array_chunk($episodes,1000,true);
        foreach ($chuckedEpisodes as $chuck)
        {
            Episode::upsert($chuck, ["id"], ["metadata_set", "description", "title", "published_at", "duration", "filesize"]);
        }

        if($this->library->episodes()->where("metadata_set", "=", false)->exists()) {
            SetInitialMetaDataJob::dispatch($this->library);
            return;
        }

        foreach($this->library->podcasts()->get() as $podcast) {

            $podcastManager = new PodcastManager($podcast);
            $podcastManager->setTotalPlaytime();
            $podcastManager->setLastEpisodeDate();
            $podcastManager->setPodcastFilesize();
            $podcastManager->setPodcastTotalEpisodes();
            unset($podcastManager);

        }



        foreach($this->library->podcasts()->get() as $podcast) {

            $podcastManager = new PodcastManager($podcast);
            $podcastManager->setPodcastImage();
            unset($podcastManager);
        }

    }
}
