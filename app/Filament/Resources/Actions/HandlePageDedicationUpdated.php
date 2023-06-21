<?php

namespace App\Filament\Resources\Actions;

use Closure;

class HandlePageDedicationUpdated
{
    public function __invoke(Closure $set, Closure $get, $state)
    {
        if ($get('document') != '' && $get('../../pdf_info') != '') {
            $json_pdfs = json_decode($get('../../pdf_info'), true);
            $search_key = $this->searchkey($json_pdfs, $get('document'));
            $img_page = (int) $get('page');

            $dedication_texts = [];
            if ($get('dedications')) {
                if (array_key_exists('dedication_texts', $get('dedications'))) {
                    $dedication_texts = $get('dedications')['dedication_texts'];
                }
            }
            $set('dedications',
                [
                    'dimensions' => $json_pdfs[$search_key]['dimensions'],
                    'page' => asset($json_pdfs[$search_key]['pdf'][$img_page]),
                    'type' => $json_pdfs[$search_key]['type'],
                    'page_number' => $get('page'),
                    'dedication_texts' => $dedication_texts,
                ]);
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
