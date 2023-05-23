<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class DownloadsCleanUp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'downloads:clean';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean Download Generated PDFs';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $files = Storage::files('downloads');
        Storage::delete($files);

        return Command::SUCCESS;
    }
}
