<?php

namespace App\Filament\Resources\Actions;

use Closure;

class SetPdfDataForBarcodePositioner
{
    public function __invoke(Closure $set, Closure $get, $state)
    {
        if ($get('page') != '') {
            $json_pdfs = json_decode($get('../../pdf_info'), true);
            $search_key = $this->searchkey($json_pdfs, $get('document'));
            //$json_pdfs[$search_key]['type'] = $get('page');
            if ($search_key) {
                $img_page = (int) $get('page');

                $repeater_fields = $get('barcode_info');
                $new_fields = [];
                foreach ($repeater_fields as $key => $field) {
                    array_push($new_fields, ['field_key' => $key, 'value' => $field]);
                }

                return [
                    'barcode_info' => $new_fields,
                    'dimensions' => $json_pdfs[$search_key]['dimensions'],
                    'page' => asset($json_pdfs[$search_key]['pdf'][$img_page]),
                ];
            }
        }

        return [];
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
