<?php

namespace App\Jobs;

use App\Directory;
use App\Library;
use App\Managers\LibraryManager;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Termwind\Components\Li;

class AddLibraryDirectoryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private readonly Library $library, private readonly Directory $directory)
    {
    }

    public function handle(): void
    {
        $libraryManager = new LibraryManager($this->library);
        $libraryManager->rescanSingleDirectory($this->directory);
        unset($libraryManager);
    }
}
