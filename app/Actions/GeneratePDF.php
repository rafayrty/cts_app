<?php

namespace App\Actions;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class GeneratePDF
{
    public function __invoke($file, $pages, $fonts)
    {
        $response = Http::attach('file', file_get_contents($file), 'simple.pdf')->post('http://127.0.0.1:8080/generate', [
            ['name' => 'pages', 'contents' => json_encode(['pages' => json_encode($pages), 'fonts' => json_encode($fonts)])],
        ]);

        if ($response->failed()) {
            throw new RuntimeException('Failed to connect ', $response->status());
        }

        return $response;
    }
}
