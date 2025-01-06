<?php

namespace App\Managers;

use App\Directory;
use App\Episode;
use App\Image;
use App\Jobs\RefreshPodcastJob;
use App\Log;
use App\Metadata;
use App\Podcast;

class DirectoryManager
{
    private Directory $directory;
    public function __construct(Directory $directory)
    {
        $this->directory = $directory;
    }

    public function rescanDirectory()
    {

        $subdirectories = scandir($this->directory->path);

        foreach($subdirectories as $subdirectory) {

            if($subdirectory == '.' || $subdirectory == '..') {
                continue;
            }

            $subdirectoryPath = $this->directory->path . DIRECTORY_SEPARATOR . $subdirectory;

            if(is_dir($subdirectoryPath)) {

                if(Podcast::where("path", "=", $subdirectoryPath)->count() == 0) {
                    $podcast = new Podcast();
                    $podcast->name = $subdirectory;
                    $podcast->path = $subdirectoryPath;
                    $podcast->directory_id = $this->directory->id;
                    $podcast->library_id = $this->directory->library_id;
                    $podcast->save();
                    Log::log("Podcast added: " . $podcast->name, "Directory Scan", "info");
                    RefreshPodcastJob::dispatch($podcast);
                }else {
                    $podcast = Podcast::where("path", "=", $subdirectoryPath)->get();
                    foreach ($podcast as $pod) {
                        RefreshPodcastJob::dispatch($pod);
                    }
                }

              /*  $podcastManager = new PodcastManager(Podcast::where("path", "=", $subdirectoryPath)->where("directory_id", "=", $this->directory->id)->first());
                $podcastManager->rescanPodcastDirectory();
                unset($podcastManager);
*/
            }

        }



        return true;

    }

    public function removeDirectory()
    {
        Image::where("directory_id", "=", $this->directory->id)->delete();
        Metadata::where("directory_id", "=", $this->directory->id)->delete();
        Episode::where("directory_id", "=", $this->directory->id)->delete();
        Podcast::where("directory_id", "=", $this->directory->id)->delete();
        $this->directory->delete();
    }
}
