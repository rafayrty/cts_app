<?php

namespace App\Filament\Resources\Actions;

use Closure;

class HandlePdfPositionerUpdate
{
    public function __invoke(Closure $set, Closure $get, $state)
    {
        $predefined_texts = $state['predefined_texts'];
        foreach ($predefined_texts as $text) {
            $set('predefined_texts.'.$text['field_key'].'.max_width', $text['value']['max_width']);
            $set('predefined_texts.'.$text['field_key'].'.width_percent', $text['value']['width_percent']);
            $set('predefined_texts.'.$text['field_key'].'.X_coord', $text['value']['X_coord']);
            $set('predefined_texts.'.$text['field_key'].'.Y_coord', $text['value']['Y_coord']);
            $set('predefined_texts.'.$text['field_key'].'.X_coord_percent', $text['value']['X_coord_percent']);
            $set('predefined_texts.'.$text['field_key'].'.Y_coord_percent', $text['value']['Y_coord_percent']);
            $set('predefined_texts.'.$text['field_key'].'.text', $text['value']['text']);
        }
    }
}
