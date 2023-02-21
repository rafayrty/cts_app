<?php

namespace App\Filament\Resources\Actions;

use Closure;
use Illuminate\Support\Facades\Storage;
//use Spatie\PdfToImage\Pdf as ConvertToImage;
use Karkow\MuPdf\Pdf;

class HandleProductAttatchment
{
    public function __invoke(Closure $set, Closure $get, $state)
    {
        $name = $state->getClientOriginalName();
        if ($name != $get('pdf_name') && $get('../../pdf_info') != '' && $get('pdf_name') != null) {
            $pdfs = json_decode($get('../../pdf_info'), true);
            $key = array_search($get('pdf_name'), array_column($pdfs, 'filename'));

            unset($pdfs[$key]);
            $set('../../pdf_info', json_encode($pdfs));
        }
        //$s3_file = Storage::disk('local')->get($state->path());
        //$s3 = Storage::disk('local');
        //$filename = 'temp/'.time().'.pdf';
        //$s3->put($filename, $s3_file);
        $path = Storage::disk('local')->path($state->path());
        //$pdf = new ConvertToImage($path);
        //$pdf = new ConvertToImage($state->path());
        //$pdf = new Pdf($state->path(),'/opt/homebrew/bin/mutool');
        $pdf = new Pdf($state->path(), config('app.mupdf_path'));
        $set('pdf_name', $name);
        if ($get('../../pdf_info') != '') {
            $pdfs = json_decode($get('../../pdf_info'), true);
        } else {
            $pdfs = [];
        }
        $images = [];
        $count = $this->getPDFPages($state->path());
        $dimensions = $this->getDimensions($state->path());
        $set('dimensions', $dimensions);
        for ($i = 1; $i <= $count; $i++) {
            $img_path = 'uploads/'.time().$get('type').'-'.$i.'.png';
            $pdf->setPage($i)
                ->saveImage($img_path);
            $images[] = $img_path;
        }
        array_push($pdfs, ['filename' => $name, 'dimensions' => json_encode($get('dimensions')), 'type' => $get('type'), 'name' => $get('name'), 'pdf' => $images]);
        $set('../../pdf_info', json_encode($pdfs));
    }

public function getDimensions($document)
{
    $cmd = config('app.pdf_info_path');
    exec("$cmd \"$document\" 2>&1", $output);

    $dimensions = [];

    foreach ($output as $op) {
        //Extract the number

        //if (preg_match('~Page size:\s+([0-9\.]+) x ([0-9\.]+) pts \(rotated 0 degrees\)~', $op, $matches) === 1) {
        if (preg_match('~Page size:\s+([0-9\.]+) x ([0-9\.]+) pts \(rotated 0 degrees\)~', $op, $matches) === 1) {
            $dimensions['width'] = intval($matches[1]);
            $dimensions['height'] = intval($matches[2]);
            break;
        }

        if (preg_match('~Page size:\s+([0-9\.]+) x ([0-9\.]+) pts~', $op, $matches) === 1) {
            $dimensions['width'] = intval($matches[1]);
            $dimensions['height'] = intval($matches[2]);
            break;
        }
    }

    return $dimensions;
}

public function getPDFPages($document)
{
    $cmd = config('app.pdf_info_path');
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
