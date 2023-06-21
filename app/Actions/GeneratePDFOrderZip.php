<?php

namespace App\Actions;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class GeneratePDFOrderZip
{
    public function __invoke($files, $zip_name)
    {

        $response = Http::timeout(120)->post('http://127.0.0.1:8080/generate_order_zip', [
            'files' => json_encode($files),
            'zip_name' => $zip_name
        ]);

        if ($response->failed()) {
            throw new RuntimeException('Failed to connect ', $response->status());
        }

        return $response;
    }
}
