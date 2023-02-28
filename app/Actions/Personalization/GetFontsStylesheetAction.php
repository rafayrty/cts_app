<?php

namespace App\Actions\Personalization;

use App\Models\Fonts;

class GetFontsStylesheetAction
{
    public function __invoke()
    {
        $fonts = Fonts::all();
        $css = "@font-face{
          font-family: GE-Dinar-Medium;
          src: url('fonts/GE-Dinar-One-Medium.ttf');
        }";

        foreach ($fonts as $font) {
            $css .= "
        @font-face{
            font-family: '".$font->font_name."';
            src: url('".public_path($font->attatchment)."');
        }
      ";
        }

        $this->create_stylesheet($css);
    }

    public function create_stylesheet($css)
    {
        $file_path = 'fonts.css';

        // Write the CSS string to a new file
        file_put_contents($file_path, $css);

        return $file_path;
    }
}
