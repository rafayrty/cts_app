<?php

namespace App\Filament\Resources\Actions;

use Closure;

class HandleEditorPageNumber
{
    public function __invoke(Closure $set, Closure $get, $state): string
    {

        if ($state['document']) {
            $json_pdfs = json_decode($get('pdf_info'), true);
            if ($json_pdfs) {
                foreach ($json_pdfs as $pdf) {
                    if ($pdf['filename'] == $state['document']) {
                        return 'Page #'.((int) $state['page'] + 1).' Of '.$pdf['name'];
                    }
                }
            }
        }

        return '';
    }
}
