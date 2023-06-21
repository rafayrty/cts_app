<?php

namespace App\Actions;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class GeneratePDFOrder
{
    public function __invoke($file, $pages, $dedications, $barcodes, $fonts)
    {

        $response = Http::attach('file', file_get_contents($file), 'simple.pdf')->post('http://127.0.0.1:8080/generate_order', [
            ['name' => 'pages', 'contents' => json_encode(['barcodes' => json_encode($barcodes), 'pages' => json_encode($pages), 'fonts' => json_encode($fonts), 'dedications' => json_encode($dedications)])],
        ]);

        if ($response->failed()) {
            throw new RuntimeException('Failed to connect ', $response->status());
        }

        return $response;
    }
}
