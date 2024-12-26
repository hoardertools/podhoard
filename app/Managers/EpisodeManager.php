<?php

namespace App\Managers;

use App\Episode;
use App\Metadata;

class EpisodeManager
{
    private Episode $episode;
    public function __construct(Episode $episode)
    {
        $this->episode = $episode;
    }

    public static function getSupportedFileFormats(){
        return [
            "mp3",
            "m4a",
            "wmv",
        ];
    }

    public static function addNewEpisode($podcast, $file, $returnDontSave = false){
        $fileExtension = pathinfo($file, PATHINFO_EXTENSION);

        if(in_array(strtolower($fileExtension), self::getSupportedFileFormats())){

            if(Episode::where("path", "=", $podcast->path . DIRECTORY_SEPARATOR . $file)->count() > 0){
                return true;
            }

            $episode = new Episode();
            $episode->name = pathinfo($file, PATHINFO_FILENAME);
            $episode->path = $podcast->path . DIRECTORY_SEPARATOR . $file;
            $episode->podcast_id = $podcast->id;
            $episode->directory_id = $podcast->directory_id;
            $episode->library_id = $podcast->library_id;
            $episode->metadata_set = false;
            $episode->created_at = now();
            $episode->updated_at = now();

            if($returnDontSave){
                return $episode;
            }else{
                $episode->save();
            }

        }

        return true;

    }

    public function setMetaData($returnDontSave = false){

        $metadata = new MetadataManager($this->episode);
        $metadata->setDbMetaData();
        unset($metadata);

        $this->episode->metadata_set = true;
        $this->episode->save();

        return true;
    }

}
