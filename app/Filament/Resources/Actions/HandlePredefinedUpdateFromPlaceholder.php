<?php

namespace App\Filament\Resources\Actions;

use Closure;
use Illuminate\Support\HtmlString;

class HandlePredefinedUpdateFromPlaceholder
{
    public function __invoke(Closure $set, Closure $get, $state)
    {
        if ($get('document') != '' && $get('../../pdf_info') != '') {
            $json_pdfs = json_decode($get('../../pdf_info'), true);
            $key = array_search($get('document'), array_column($json_pdfs, 'filename'));
            $json_pdfs[$key]['type'] = $state;
            $img_page = (int) $get('page');

            $repeater_fields = $get('predefined_texts');
            $new_fields = [];
            foreach ($repeater_fields as $key => $field) {
                array_push($new_fields, ['field_key' => $key, 'value' => $field]);
            }
            $set('image',
                [
                    'predefined_texts' => $new_fields,
                    'page' => asset($json_pdfs[0]['pdf'][$img_page]),
                ]);
        }

        return new HtmlString('<h1 class="bg-gray-200 p-2 rounded-md font-semibold dark:bg-gray-900">Please put this code {basmti} in the position that will be modified by the user</h1>');
    }
}
