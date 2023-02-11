<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class GeneratePDF implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $file)
    {
        $this->file = $file;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $response = Http::attach('file', file_get_contents($this->file))->post('https://jsonplaceholder.typicode.com/posts', [
            'file' => $this->file,
        ]);

        if ($response->failed()) {
            //now()->addSeconds(15 * $this->attempts());
            // The attempts() method returns the number of times the job
            // has been attempted. If it's the first run, attempts() will return 1.

            throw new RuntimeException('Failed to connect ', $response->status());
        }
    }
}
