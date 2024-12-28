<?php

namespace App\Managers;

use App\Episode;
use App\Image;
use App\Metadata;
use App\Standards\MetadataClass;
use Kiwilan\Audio\Audio;

class MetadataManager
{
    private Episode $episode;
    public function __construct(Episode $episode)
    {
        $this->episode = $episode;
    }

    public function setDbMetaData($returnDontSave = false)
    {

        if($returnDontSave){
            $returnObject = new \stdClass();

        }
        $meta = (array) $this->getFileMetaData();

        $episodeMetaData = [
            'description',
            'title',
            'published_at',
            'duration',
            'filesize'
        ];

        $existingMetadata = $this->episode->metadata()->get(["episode_id", "key"])->groupBy('key');

        foreach($meta as $key => $value) {
            if (strlen($value) > 0) {

                if(in_array($key, $episodeMetaData)){

                    //Don't override existing / RSS metadata
                    if($key == 'published_at'){
                        if($this->episode->published_at == null){
                            $this->episode->published_at = $value;
                        }
                    }else{
                        $this->episode->{$key} = $value ?? '';
                    }

                }else{
                    if (!isset($existingMetadata[$this->episode->id][$key])) {
                        $metadata = new Metadata();
                        $metadata->episode_id = $this->episode->id;
                        $metadata->podcast_id = $this->episode->podcast_id;
                        $metadata->directory_id = $this->episode->directory_id;
                        $metadata->library_id = $this->episode->library_id;
                        $metadata->key = $key;
                        $metadata->value = $value;

                        if($returnDontSave) {
                            $returnObject->metaData[] = $metadata;
                        }else{
                            $metadata->save();
                        }

                        unset($metadata);

                    }
                }

            }
        }

        foreach( $episodeMetaData as $key){
            if(!isset($meta[$key])){
                $meta[$key] = '';
            }
        }

        $this->episode->metadata_set = true;
        if($returnDontSave){
            $returnObject->episode = $this->episode;
            return $returnObject;
        }else{
            $this->episode->save();
        }

        unset($meta);

        return true;

    }

    public function getFileMetaData(){

        $audio = Audio::read($this->episode->path);

        $metadataAudio = $audio->getMetadata();
        $meta = new MetadataClass();
        $meta->title = $audio->getTitle() ?? '';
        $meta->author = $audio->getArtist() ?? '';
        $meta->description = $audio->getDescription() ?? '';
        try{
            $meta->year = $audio->getYear() ?? '';
        }catch (\TypeError $e){
            $meta->year = 0;
        }
        $meta->language = $audio->getLanguage() ?? '';
        $meta->published_at = $audio->getCreationDate() ?? '';
        $meta->duration = round($audio->getDuration()) ?? 0;
        $meta->filesize = $metadataAudio->getFileSize() ?? 0;

        if($meta->description == ''){
            $meta->description = $audio->getComment() ?? '';
        }

        if($meta->published_at == ''){

            $meta->published_at = $audio->getRawAll()['id3v2']['year'] ?? '';
        }
        unset($audio);

        return $meta;

    }

}
