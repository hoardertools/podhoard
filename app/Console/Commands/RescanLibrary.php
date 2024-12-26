<?php

namespace App\Console\Commands;

use App\Directory;
use App\Jobs\RescanLibraryJob;
use App\Library;
use Illuminate\Console\Command;

class RescanLibrary extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:rescan-library';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        foreach(Library::get() as $library){
            RescanLibraryJob::dispatch($library);
        }
    }
}
