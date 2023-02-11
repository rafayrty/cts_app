<?php

namespace App\Filament\Resources\Actions;

use Closure;
use Illuminate\Support\Facades\Storage;
use Spatie\PdfToImage\Pdf as ConvertToImage;

class HandleProductAttatchment
{
    public function __invoke(Closure $set, Closure $get, $state)
    {
        $name = $state->getClientOriginalName();
        //$s3_file = Storage::disk('local')->get($state->path());
        //$s3 = Storage::disk('local');
        //$filename = 'temp/'.time().'.pdf';
        //$s3->put($filename, $s3_file);
        $path = Storage::disk('local')->path($state->path());
        //$pdf = new ConvertToImage($path);
        $pdf = new ConvertToImage($state->path());
        if ($get('../../pdf_info') != '') {
            $pdfs = json_decode($get('../../pdf_info'), true);
        } else {
            $pdfs = [];
        }
        $images = [];
        $count = $this->getPDFPages($state->path());
        for ($i = 1; $i <= $count; $i++) {
            $img_path = 'uploads/'.time().'.jpg';
            $pdf->setPage($i)
                ->saveImage($img_path);
            $images[] = $img_path;
        }
        array_push($pdfs, ['filename' => $name, 'type' => $get('type'), 'name' => $get('name'), 'pdf' => $images]);
        $set('../../pdf_info', json_encode($pdfs));
    }

// Make a function for convenience
public function getPDFPages($document)
{
    $cmd = '/opt/homebrew/bin/pdfinfo';           // Linux
    //$cmd = "C:\\path\\to\\pdfinfo.exe";  // Windows

    // Parse entire output
    // Surround with double quotes if file name has spaces

    exec("$cmd \"$document\" 2>&1", $output);
    $pagecount = 0;
    foreach ($output as $op) {
        // Extract the number
        if (preg_match("/Pages:\s*(\d+)/i", $op, $matches) === 1) {
            $pagecount = intval($matches[1]);
            break;
        }
    }

    return $pagecount;
}
}
