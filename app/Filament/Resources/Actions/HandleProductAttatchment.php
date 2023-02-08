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
        $set('pdf_name', $name);
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
        for ($i = 1; $i <= $pdf->getNumberOfPages(); $i++) {
            $img_path = 'uploads/'.time().'.jpg';
            $pdf->setPage($i)
                ->saveImage($img_path);
            $images[] = $img_path;
        }
        array_push($pdfs, ['filename' => $name, 'type' => $get('type'), 'name' => $get('name'), 'pdf' => $images]);
        $set('../../pdf_info', json_encode($pdfs));
        //Set barcode Location
        //if (count($pdfs) > 1) {
        //$location = ['first' => 'First Page', 'last' => 'Last Page', 'both' => 'Both First & Last'];
        //$set('barcode_settings.location', $location);
        //} else {
        //$location = ['first' => 'First Page', 'last' => 'Last Page'];
        //$set('barcode_settings.location', $location);
        //}
    }
}
