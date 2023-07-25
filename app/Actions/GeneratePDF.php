<?php

namespace App\Actions;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class GeneratePDF
{
    public function __invoke($file, $pages, $dedications, $fonts)
    {
        $response = Http::attach('file', file_get_contents($file), 'simple.pdf')->post('http://127.0.0.1:8080/generate', [
            ['name' => 'pages', 'contents' => json_encode(['pages' => json_encode($pages), 'fonts' => json_encode($fonts), 'dedications' => json_encode($dedications)])],
        ]);

        if ($response->failed()) {
            throw new RuntimeException('Unable to Generate Invoice', $response->status());
        }

        return $response;
    }
}
