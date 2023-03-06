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
            $barcodes = [];
            if ($get('barcodes')) {
                if (array_key_exists('barcodes', $get('barcodes'))) {
                    $barcodes = $get('barcodes')['barcodes'];
                }
            }

            $set('barcodes',
                [
                    'dimensions' => $json_pdfs[$search_key]['dimensions'],
                    'page' => asset($json_pdfs[$search_key]['pdf'][$img_page]),
                    'type' => $json_pdfs[$search_key]['type'],
                    'page_number' => $get('page'),
                    'barcodes' => $barcodes,
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
