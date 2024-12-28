<?php

namespace App\Managers;

use App\Episode;
use App\Image;
use App\Metadata;
use App\Podcast;
use Kiwilan\Audio\Audio;

class PodcastManager
{
    private Podcast $podcast;
    public function __construct(Podcast $podcast)
    {
        $this->podcast = $podcast;
    }

    public function rescanPodcastDirectory(){

        $files = scandir($this->podcast->path);

        $newEpisodes = [];

        foreach($files as $file){

            if($file == '.' || $file == '..') {
                continue;
            }

            if(is_file($this->podcast->path . "/" . $file)){

                $newEpisode = EpisodeManager::addNewEpisode($this->podcast, $file, true);

                if($newEpisode instanceof Episode){
                    $newEpisodes[] = $newEpisode->toArray();
                }
            }
        }

        if (!empty($newEpisodes)) {
            $chuckedEpisodes = array_chunk($newEpisodes, 1000, true);
            foreach ($chuckedEpisodes as $newEpisodesChuck) {
                Episode::insert($newEpisodesChuck);
            }

        }

        $this->podcast->total_episodes = $this->podcast->episodes()->count();
        $this->podcast->last_scanned_at = now();
        $this->podcast->save();
        return true;
    }

    public function setPodcastImage(){

        if(is_null($this->podcast->image_id)){

            foreach($this->podcast->episodes()->get() as $episode){
                $audio = Audio::read($episode->path);

                if($audio->getCover() != null){

                    $image = new Image();
                    $image->base64 = $audio->getCover()->getContents(true);
                    $image->type = "Podcast";
                    $image->library_id = $episode->library_id;
                    $image->directory_id = $episode->directory_id;
                    $image->podcast_id = $episode->podcast_id;
                    $image->save();
                    $this->podcast->image_id = $image->id;
                    $this->podcast->save();

                    break;
                }

            }



        }

    }

    public function setTotalPlaytime(){

        $playtime = 0;

        foreach($this->podcast->episodes as $episode){
                $playtime = $playtime + $episode->duration;
        }

        $this->podcast->total_playtime = round($playtime);
        $this->podcast->save();

    }

    public function setLastEpisodeDate(){

        $lastEpisode = $this->podcast->episodes()->orderBy('created_at', 'desc')->first();

        if($lastEpisode){
            $this->podcast->latest_addition_at = $lastEpisode->created_at;
            $this->podcast->save();
        }

    }

    public function setPodcastFilesize(){

        $size = 0;

        foreach($this->podcast->episodes as $episode){
                $size = $size + $episode->filesize;
        }

        $this->podcast->total_size = $size;
        $this->podcast->save();

    }

    public function setPodcastTotalEpisodes(){

        $this->podcast->total_episodes = $this->podcast->episodes()->count();
        $this->podcast->save();

    }

    public function setPodcastMetadata(){

        $metaData = [];
        $episodes = [];

        foreach($this->podcast->episodes()->where("metadata_set", "=", false)->get() as $episode) {

            $metadataManager = new MetadataManager($episode);
            $metaObject = $metadataManager->setDbMetaData(true);
            foreach($metaObject->metaData as $metadata) {
                $metaData[] = $metadata->toArray();
            }

            $episodes[] = $metaObject->episode->toArray();
//$metaObject->episode->save();
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

        foreach($this->podcast->episodes()->get() as $episode) {

            if ($episode->duration == 0 or $episode->filesize == 0) {
                $audio = Audio::read($episode->path);
                $episode->duration = round($audio->getDuration());
                $episode->filesize = filesize($episode->path);
                $episode->save();
            }
        }

    }


    public function refresh(){

        $this->rescanPodcastDirectory();
        $this->setPodcastImage();
        $this->setPodcastMetadata();
        $this->setTotalPlaytime();
        $this->setLastEpisodeDate();
        $this->setPodcastFilesize();
        $this->setPodcastTotalEpisodes();

    }
}
