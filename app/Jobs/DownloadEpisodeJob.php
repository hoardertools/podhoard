<?php

namespace App\Jobs;

use App\Directory;
use App\Download;
use App\DownloadLog;
use App\Episode;
use App\Log;
use App\Managers\EpisodeManager;
use App\Managers\PodcastManager;
use App\Podcast;
use App\Setting;
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

            if($this->globalRateLimitExceeded()){
                \Log::warning("Global download rate limit exceeded, waiting 5 seconds before trying again");
                Log::log("Global download rate limit exceeded, waiting 5 seconds before trying again", "Episode Download", "warning");
                sleep(5);
                DownloadEpisodeJob::dispatch()->onQueue("downloads");
                return;
            }
            $episode = Download::orderBy("created_at", "ASC")->first();

            if($this->perHostRateLimitExceeded(parse_url($episode->download_url, PHP_URL_HOST))){
                \Log::warning("Per-host download rate limit exceeded, waiting 5 seconds before trying again");
                Log::log("Per-host download rate limit exceeded, waiting 5 seconds before trying again", "Episode Download", "warning" );
                sleep(5);
                DownloadEpisodeJob::dispatch()->onQueue("downloads");
                return;
            }

            try{
                if(Setting::where("key", "CustomUserAgent")->where("value", "!=", "")->exists()){
                    $opts = [
                        "http" => [
                            "header" => "User-Agent: " . Setting::where("key", "CustomUserAgent")->first()->value
                        ]
                    ];
                    $context = stream_context_create($opts);
                    $download = file_get_contents($episode->download_url, false, $context);
                }else{
                    $download = file_get_contents($episode->download_url);
                }

            }catch (\Exception $e){

                \Log::error("Failed to download episode: " . $episode->id . PHP_EOL . "Error: " . $e->getMessage());
                Log::log("Failed to download episode: " . $episode->id . PHP_EOL . "Error: " . $e->getMessage(),  "Episode Download", "error");
                $dl = new DownloadLog();
                $dl->download_id = $episode->id;
                $dl->error = true;
                $dl->downloaded = false;
                $dl->download_url = $episode->download_url;
                $dl->download_host = parse_url($episode->download_url, PHP_URL_HOST);
                $dl->save();
                unset($dl);
                $episode->delete();
                DownloadEpisodeJob::dispatch()->onQueue("downloads");
                unset($e);
                return;
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

                Log::log("Downloaded episode: " . $episode->id . " from " . $episode->download_url . " to " . $episode->path,  "Episode Download", "info");
                $dl = new DownloadLog();
                $dl->download_id = $episode->id;
                $dl->error = false;
                $dl->downloaded = true;
                $dl->download_url = $episode->download_url;
                $dl->download_host = parse_url($episode->download_url, PHP_URL_HOST);
                $dl->save();
                unset($dl);

                $meta = new EpisodeManager($newEpisode);
                $meta->setMetaData();

                $podcastManager = new PodcastManager($podcast);
                $podcastManager->refresh();


                $episode->delete();
            }

            DownloadEpisodeJob::dispatch()->onQueue("downloads");

        }
    }

    public function globalRateLimitExceeded(): bool
    {
        $downloads = DownloadLog::where("created_at", ">=", \Carbon\Carbon::now()->subSeconds(60))->count();
        $maxDownloads = Setting::where("key", "GlobalDownloaderRateLimit")->first()->value;
        if($maxDownloads === 0){
            return false;
        }
        if($downloads >= $maxDownloads){
            return true;
        }
        return false;
    }

    public function perHostRateLimitExceeded(string $host): bool
    {
        $downloads = DownloadLog::where("created_at", ">=", \Carbon\Carbon::now()->subSeconds(60))->where("download_host", "=", $host)->count();
        $maxDownloads = Setting::where("key", "PerHostDownloaderRateLimit")->first()->value;
        if($maxDownloads === 0){
            return false;
        }
        if($downloads >= $maxDownloads){
            return true;
        }
        return false;

    }

    function sanitizeFileName(string $name, int $maxLength = 255): string
    {
        // Remove leading and trailing whitespace, tabs, and linebreaks
        $sanitized = preg_replace('/^\s+|\s+$/u', '', $name);

        // Replace invalid characters with an underscore
        $sanitized = preg_replace('/[^a-zA-Z0-9\-_\.]/u', '_', $sanitized);

        // Prevent multiple underscores from consecutive invalid characters
        $sanitized = preg_replace('/_+/', '_', $sanitized);

        // Limit the length of the name to the specified maximum
        if (strlen($sanitized) > $maxLength) {
            $sanitized = substr($sanitized, 0, $maxLength);
        }

        // Ensure the name is not empty (use "default" if the sanitized name is empty)
        if (empty($sanitized)) {
            $sanitized = 'default';
        }

        return $sanitized;
    }
}
