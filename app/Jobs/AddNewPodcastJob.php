<?php

namespace App\Jobs;

use App\Http\Controllers\LibraryController;
use App\Library;
use App\Podcast;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AddNewPodcastJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private readonly string $rss, private readonly Library $library)
    {
    }

    public function handle(): void
    {

        $libraryController = new LibraryController();
        $libraryController->addNewPodcast($this->rss, $this->library);

    }
}
