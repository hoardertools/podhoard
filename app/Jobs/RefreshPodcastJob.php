<?php

namespace App\Jobs;

use App\Managers\PodcastManager;
use App\Podcast;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RefreshPodcastJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private readonly Podcast $podcast)
    {
    }

    public function handle(): void
    {
        $podcastManager = new PodcastManager($this->podcast);
        $podcastManager->refresh();

    }
}
