<?php

namespace App\Filament\Resources\Actions;

use Closure;

class SetPdfDataForDedicationPositioner
{
    public function __invoke(Closure $set, Closure $get, $state)
    {
        if ($get('page') != '') {
            $json_pdfs = json_decode($get('../../pdf_info'), true);
            $search_key = array_search($get('document'), array_column($json_pdfs, 'filename'));
            $json_pdfs[$search_key]['type'] = $get('page');
            $img_page = (int) $get('page');

            $repeater_fields = $get('dedication_texts');
            $new_fields = [];
            foreach ($repeater_fields as $key => $field) {
                array_push($new_fields, ['field_key' => $key, 'value' => $field]);
            }

            return [
                'dedication_texts' => $new_fields,
                'page' => asset($json_pdfs[$search_key]['pdf'][$img_page]),
                //'text_align' => $get('text_align'),
                //'X' => $get('X_coord'),
                //'Y' => $get('Y_coord'),
                //'width' => $get('max_width'),
                //'font_size' => $get('font_size'),
                //'color' => $get('color'),
            ];
        }

        return [];
    }
}
