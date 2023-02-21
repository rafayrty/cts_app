<?php

namespace App\Filament\Resources\Actions;

use Closure;

class SetPdfDataForDedicationPositioner
{
    public function __invoke(Closure $set, Closure $get, $state)
    {
        if ($get('page') != '') {
            $json_pdfs = json_decode($get('../../pdf_info'), true);
            $search_key = $this->searchkey($json_pdfs, $get('document'));
            if ($search_key != '') {
                //$json_pdfs[$search_key]['type'] = $get('page');
                $img_page = (int) $get('page');

                $repeater_fields = $get('dedication_texts');
                $new_fields = [];
                foreach ($repeater_fields as $key => $field) {
                    array_push($new_fields, ['field_key' => $key, 'value' => $field]);
                }

                return [
                    'dedication_texts' => $new_fields,
                    'dimensions' => $json_pdfs[$search_key]['dimensions'],
                    'page' => asset($json_pdfs[$search_key]['pdf'][$img_page]),
                    'page_number' => $get('page'),
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
