<?php

namespace App\Filament\Resources\Actions;

use Closure;

class SetPdfDataForPositioner
{
    public function __invoke(Closure $set, Closure $get, $state)
    {
        if ($get('page') != '') {
            $json_pdfs = json_decode($get('../../pdf_info'), true);
            $search_key = $this->searchkey($json_pdfs, $get('document'));
            $json_pdfs[$search_key]['type'] = $get('page');
            $img_page = (int) $get('page');

            $repeater_fields = $get('predefined_texts');
            $new_fields = [];
            foreach ($repeater_fields as $key => $field) {
                array_push($new_fields, ['field_key' => $key, 'value' => $field]);
            }

            return [
                'predefined_texts' => $new_fields,
                'page' => asset($json_pdfs[$search_key]['pdf'][$img_page]),
            ];
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