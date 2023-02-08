<?php

namespace App\Filament\Resources\Actions;

use Closure;

class HandlePageDedicationUpdated
{
    public function __invoke(Closure $set, Closure $get, $state)
    {
        if ($get('document') != '' && $get('../../pdf_info') != '') {
            $json_pdfs = json_decode($get('../../pdf_info'), true);
            $key = array_search($get('document'), array_column($json_pdfs, 'filename'));
            $json_pdfs[$key]['type'] = $state;
            $img_page = (int) $get('page');

            $repeater_fields = $get('dedication_texts');
            $new_fields = [];
            foreach ($repeater_fields as $key => $field) {
                array_push($new_fields, ['field_key' => $key, 'value' => $field]);
            }
            $set('image',
                [
                    'dedication_texts' => $new_fields,
                    'page' => asset($json_pdfs[0]['pdf'][$img_page]),
                ]);
        }
    }
}
