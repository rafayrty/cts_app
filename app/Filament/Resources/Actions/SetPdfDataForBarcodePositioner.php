<?php

namespace App\Filament\Resources\Actions;

use Closure;

class SetPdfDataForBarcodePositioner
{
    public function __invoke(Closure $set, Closure $get, $state)
    {
        if ($get('page') != '') {
            $json_pdfs = json_decode($get('../../pdf_info'), true);
            $key = array_search($get('document'), array_column($json_pdfs, 'filename'));
            $json_pdfs[$key]['type'] = $get('page');
            $img_page = (int) $get('page');

            $repeater_fields = $get('barcode_info');
            $new_fields = [];
            foreach ($repeater_fields as $key => $field) {
                array_push($new_fields, ['field_key' => $key, 'value' => $field]);
            }
            return [
                'barcode_info' => $new_fields,
                'page' => asset($json_pdfs[0]['pdf'][$img_page]),
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
