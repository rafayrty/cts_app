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
            foreach ($json_pdfs as $pdf) {
                if ($pdf['name']) {
                    $array[] = $pdf['name'];
                }
            }

            return $array;
        }
    }
}
