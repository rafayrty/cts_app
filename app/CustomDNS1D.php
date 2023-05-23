<?php
namespace App;

use Illuminate\Support\Str;

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DNS1D
 *
 * @author dinesh
 */
class CustomDNS1D extends \Milon\Barcode\DNS1D {

    /**
     * Return a PNG image representation of barcode (requires GD or Imagick library).
     * @param $code (string) code to print
     * @param $type (string) type of barcode: <ul><li>C39 : CODE 39 - ANSI MH10.8M-1983 - USD-3 - 3 of 9.</li><li>C39+ : CODE 39 with checksum</li><li>C39E : CODE 39 EXTENDED</li><li>C39E+ : CODE 39 EXTENDED + CHECKSUM</li><li>C93 : CODE 93 - USS-93</li><li>S25 : Standard 2 of 5</li><li>S25+ : Standard 2 of 5 + CHECKSUM</li><li>I25 : Interleaved 2 of 5</li><li>I25+ : Interleaved 2 of 5 + CHECKSUM</li><li>C128 : CODE 128</li><li>C128A : CODE 128 A</li><li>C128B : CODE 128 B</li><li>C128C : CODE 128 C</li><li>EAN2 : 2-Digits UPC-Based Extention</li><li>EAN5 : 5-Digits UPC-Based Extention</li><li>EAN8 : EAN 8</li><li>EAN13 : EAN 13</li><li>UPCA : UPC-A</li><li>UPCE : UPC-E</li><li>MSI : MSI (Variation of Plessey code)</li><li>MSI+ : MSI + CHECKSUM (modulo 11)</li><li>POSTNET : POSTNET</li><li>PLANET : PLANET</li><li>RMS4CC : RMS4CC (Royal Mail 4-state Customer Code) - CBC (Customer Bar Code)</li><li>KIX : KIX (Klant index - Customer index)</li><li>IMB: Intelligent Mail Barcode - Onecode - USPS-B-3200</li><li>CODABAR : CODABAR</li><li>CODE11 : CODE 11</li><li>PHARMA : PHARMACODE</li><li>PHARMA2T : PHARMACODE TWO-TRACKS</li></ul>
     * @param $w (int) Width of a single bar element in pixels.
     * @param $h (int) Height of a single bar element in pixels.
     * @param $color (array) RGB (0-255) foreground color for bar elements (background is transparent).
     * @return string|false in case of error.
     * @protected
     */
    protected function getBarcodePNG($code, $type, $w = 2, $h = 30, $color = array(0, 0, 0), $showCode = false) {
        if (!$this->store_path) {
            $this->setStorPath(app('config')->get("barcode.store_path"));
        }
        $this->setBarcode($code, $type);
        // calculate image size
        $width = ($this->barcode_array['maxw'] * $w);
        $height = $h;
        if (function_exists('imagecreate')) {
            // GD library
            $imagick = false;
            $png = imagecreate($width + 10, $height + 10);
            $bgcol = imagecolorallocate($png, 255, 255, 255);
            imagecolortransparent($png, $bgcol);
            $fgcol = imagecolorallocate($png, $color[0], $color[1], $color[2]);
        } elseif (extension_loaded('imagick')) {
            $imagick = true;
            $bgcol = new \imagickpixel('rgb(255,255,255)');
            $fgcol = new \imagickpixel('rgb(' . $color[0] . ',' . $color[1] . ',' . $color[2] . ')');
            $png = new \Imagick();
            $png->newImage($width, $height, 'none', 'png');
            $bar = new \imagickdraw();
            $bar->setfillcolor($fgcol);
        } else {
            return false;
        }
        // print bars
        $x = 5;
        foreach ($this->barcode_array['bcode'] as $k => $v) {
            $bw = round(($v['w'] * $w), 3);
            $bh = round(($v['h'] * $h / $this->barcode_array['maxh']), 3);
        if($showCode)
                $bh -= imagefontheight(3) ;
            if ($v['t']) {
                $y = round(($v['p'] * $h / $this->barcode_array['maxh']), 3) + 5;
                // draw a vertical bar
                if ($imagick) {
                    $bar->rectangle($x, $y, ($x + $bw), ($y + $bh));
                } else {
                    imagefilledrectangle($png, $x, $y, ($x + $bw) - 1, ($y + $bh), $fgcol);
                }
            }
            $x += $bw;
        }
        ob_start();

    // Add Code String in bottom
        if($showCode)
            if ($imagick) {
            $bar->setTextAlignment(\Imagick::ALIGN_CENTER);
            $bar->annotation( 10 , $h - $bh +10 , $code );
        } else {
            $width_text = imagefontwidth(2) * strlen($code);
            $height_text = imagefontheight(2);
            imagestring($png, 2, ($width/2) - ($width_text/2.4) , ($height + 6 - $height_text) , $code, $fgcol);

        }
        // get image out put
        if ($imagick) {
            $png->drawimage($bar);
            echo $png;
        } else {
            imagepng($png);
            imagedestroy($png);
        }
        $image = ob_get_clean();
        $image = base64_encode($image);
        //$image = 'data:image/png;base64,' . base64_encode($image);
        return $image;
    }


}
