<?php

namespace App\Filament\Resources\Actions;

use Closure;

class HandlePageOptions
{
    public function __invoke(Closure $set, Closure $get, $state)
    {
        if ($get('../../pdf_info') != '') {
            $json_pdfs = json_decode($get('../../pdf_info'), true);
            $key = array_search($get('pdf_name'), array_column($json_pdfs, 'filename'));
            $pages_count = count($json_pdfs[$key]['pdf']);
            $array = [];
            for ($i = 1; $i <= $pages_count; $i++) {
                $array[] = $i;
            }

            return $array;
        }
    }
}
