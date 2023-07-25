<?php

namespace App\Filament\Resources\Actions;

use Closure;

class HandleDocumentOptions
{
    public function __invoke(Closure $set, Closure $get, $state)
    {
        if ($get('../../pdf_info') != '') {
            $array = [];
            $json_pdfs = json_decode($get('../../pdf_info'), true);
            if ($json_pdfs) {
                foreach ($json_pdfs as $pdf) {
                    if ($pdf['name']) {
                        //$array[] = $pdf['name'];
                        $array[$pdf['filename']] = $pdf['name'];
                    }
                }
            }

            return $array;
        }
    }
}
