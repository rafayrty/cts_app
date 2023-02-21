<?php

namespace App\Filament\Resources\Actions;

use Closure;

class HandleDocType
{
    public function __invoke(Closure $set, Closure $get, $state)
    {
        if ($get('../../pdf_info') != '') {
            $json_pdfs = json_decode($get('../../pdf_info'), true);
            $key = array_search($get('pdf_name'), array_column($json_pdfs, 'filename'));
            //$key = $this->searchKey($json_pdfs, $get('name'));
            $json_pdfs[$key]['type'] = $state;
            $set('../../pdf_info', json_encode($json_pdfs));
        }
    }

    public function searchkey($array, $search)
    {
        $key = null;
        foreach ($array as $key => $value) {
            if ($value['name'] == $search) {
                return $key;
            }
        }
    }
}
