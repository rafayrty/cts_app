<?php

namespace App\Filament\Resources\Actions;

use Closure;
use Illuminate\Support\Facades\Log;

class HandlePageOptions
{
    public function __invoke(Closure $set, Closure $get, $state)
    {
        if ($get('../../pdf_info') != '' && $get('document')) {
            $json_pdfs = json_decode($get('../../pdf_info'), true);
            //$key = array_search($get('document'), array_column($json_pdfs, 'type'));

            $key = $this->searchkey($json_pdfs, $get('document'));

            //Log::info([$get('document')]);
            $array = [];
            if (is_numeric($key)) { //Hence the key is a number including zero
                $pages_count = count($json_pdfs[$key]['pdf']);
                for ($i = 1; $i <= $pages_count; $i++) {
                    $array[] = $i;
                }
            }

            return $array;
        }
    }

    public function searchkey($array, $search)
    {
        $key = null;
        foreach ($array as $key => $value) {
            if ($value['filename'] == $search) {
                return $key;
            }
        }
    }
}
