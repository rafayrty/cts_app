<?php

namespace App\Filament\Resources\Actions;

use Closure;

class HandlePageBarcodeUpdated
{
    public function __invoke(Closure $set, Closure $get, $state)
    {
        if ($get('document') != '' && $get('../../pdf_info') != '') {
            $json_pdfs = json_decode($get('../../pdf_info'), true);
            $search_key = $this->searchkey($json_pdfs, $get('document'));
            //$json_pdfs[$search_key]['type'] = $state;
            $img_page = (int) $get('page');

            $repeater_fields = $get('barcode_info');
            $new_fields = [];
            foreach ($repeater_fields as $key => $field) {
                array_push($new_fields, ['field_key' => $key, 'value' => $field]);
            }
            $set('image',
                [
                    'barcode_info' => $new_fields,
                    'dimensions' => $json_pdfs[$search_key]['dimensions'],
                    'page' => asset($json_pdfs[$search_key]['pdf'][$img_page]),
                ]);
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
