<?php

namespace App\Filament\Resources\Actions;

use Closure;

class HandleDocumentName
{
    public function __invoke(Closure $set, Closure $get, $state)
    {
        if ($get('../../pdf_info') != '' && $get('pdf_name') != '') {
            $json_pdfs = json_decode($get('../../pdf_info'), true);
            $key = array_search($get('pdf_name'), array_column($json_pdfs, 'filename'));

            //Update Pages

            $current_name = $json_pdfs[$key]['name'];

            $pages = $get('../../pages');
            if ($json_pdfs) {
                $found_key = null;
                $new_array = [];
                $pages = $get('../../pages');
                foreach ($pages as &$page) {
                    if ($page['document'] == $current_name) {
                        $page['document'] = $state;
                    }
                }
            }
            $set('../../pages', $pages);
            $json_pdfs[$key]['name'] = $state;
            $set('../../pdf_info', json_encode($json_pdfs));
        }
    }
}
