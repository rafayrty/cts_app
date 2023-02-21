<?php

namespace App\Filament\Resources\Actions;

use App\Models\Fonts;

class SetPdfFonts
{
    public function __invoke()
    {
        $fonts = Fonts::all();
        $array = [];
        if ($fonts) {
            foreach ($fonts as $font) {
                $array[$font->font_name] = $font->font_name;
            }
            $array = array_merge($array, ['GE-Dinar-Medium' => 'GE-Dinar-Medium']);

            return $array;
        }
    }
}
