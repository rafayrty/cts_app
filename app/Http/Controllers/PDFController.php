<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use TCPDF_FONTS;
use TCPDI;

class PDFController extends Controller
{
    /**
     *      * Write code on Method
     *           *
     *                * @return response()
     *                     */
    public function product($id)
    {
        $product = Product::findOrFail($id);
        $document = $product->documents->first();
        $pages = $product->pages;
        $file = Storage::disk('local')->path('public/'.$document->attatchment);
        $outputFilePath = public_path('sample_output.pdf');
        $fpdi = new TCPDI(null, 'pt');
        $count = $fpdi->setSourceFile($file);
        for ($i = 0; $i < $count; $i++) {
            if (is_numeric($this->findKey($pages, $i + 1))) {
                $page = $pages[$this->findKey($pages, $i + 1)];
                $template = $fpdi->importPage($page['page'] + 1);
                $size = $fpdi->getTemplateSize($template);

                $orientation = ($size['w'] > $size['h']) ? 'L' : 'P';
                //$fpdi->setMargins(0,0,0,true);
                $margins = $fpdi->getMargins();
                $fpdi->AddPage($orientation, [$size['w'], $size['h']]);
                $fpdi->useTemplate($template);

                $width = $size['w'];
                $height = $size['h'];
                foreach ($page['image']['predefined_texts'] as $texts) {
                    // set some language dependent data:
                    $lg = [];
                    $lg['a_meta_charset'] = 'UTF-8';
                    //$lg['a_meta_dir'] = 'rtl';
                    $lg['a_meta_language'] = 'fa';
                    $lg['w_page'] = 'page';

                    // set some language-dependent strings (optional)
                    $fpdi->setLanguageArray($lg);
                    //$fontname = TCPDF_FONTS::addTTFfont(public_path('fonts/Alexandria-SemiBold.ttf'), 'TrueTypeUnicode', '', 32,public_path('custom-fonts/'));
                    $fpdi->SetFont('dejavusans', '', $this->pxToPt($texts['value']['font_size']));
                    // $fpdi->SetTextColor(153,0,153);

                    $fpdi->SetAutoPageBreak(false);
                    $text = trim(html_entity_decode($texts['value']['text']), " \t\n\r\0\x0B\xC2\xA0");
                    //$content_height = $fpdi->GetMultiCellHeight(150, 5, $text, 0, 'C');
                    //$fpdi->SetXY(0, $height - $content_height);
                    //$fpdi->SetXY($pages[0]['image']['predefined_texts'][0]['value']['X_coord'], 0);

                    $color = $texts['value']['color'];
                    //$fpdi->SetAlpha(0);
                    //$fpdi->setFillColor(255,255,0);
                    //$fpdi->setTextColor(0, 255, 255);

                    //$fpdi->SetAlpha(1);
                    $fpdi->SetXY($texts['value']['X_coord_percent'] / 100 * $width, $texts['value']['Y_coord_percent'] / 100 * $height);
                    //$fpdi->MultiCell(($pages[0]['image']['predefined_texts'][0]['value']['max_width'] / 1000) * $width, 4.4, $text, 0, $pages[0]['image']['predefined_texts'][0]['value']['text_align'], false, false, 'rtl');

                    $fpdi->MultiCell($this->pxToPt($texts['value']['max_width']), 5, $text, 0, $texts['value']['text_align'], 0, 0, '', '', true);
                }
            } else {
                $template = $fpdi->importPage($i + 1);
                $size = $fpdi->getTemplateSize($template);

                $orientation = ($size['w'] > $size['h']) ? 'L' : 'P';
                //$fpdi->setMargins(0,0,0,true);
                $margins = $fpdi->getMargins();
                $fpdi->AddPage($orientation, [$size['w'], $size['h']]);
                $fpdi->useTemplate($template);
            }
        }
        $fpdi->Output($outputFilePath, 'F');

        return response()->file($outputFilePath);
    }

    public function pxToPt($px)
    {
        return $pt = $px * (72 / 92);
    }

public function findKey($array, $keySearch)
{
    foreach ($array as $key => $arr) {
        if ($arr['page'] == $keySearch) {
            return $key;
        }
    }

    return false;
}

    /**
     *      * Write code on Method
     *           *
     *                * @return response()
     *                     */
    public function index(Request $request)
    {
        $filePath = public_path('sample.pdf');
        $outputFilePath = public_path('sample_output.pdf');
        $this->fillPDFFile($filePath, $outputFilePath);

        return response()->file($outputFilePath);
    }

    /**
     *      * Write code on Method
     *           *
     *                * @return response()
     *                     */
    public function fillPDFFile($file, $outputFilePath)
    {
        $fpdi = new TCPDI();

        $count = $fpdi->setSourceFile($file);

        $template = $fpdi->importPage(1);
        $size = $fpdi->getTemplateSize($template);

        $size = $fpdi->getTemplateSize($template);

        $orientation = ($size['w'] > $size['h']) ? 'L' : 'P';
        $fpdi->AddPage($orientation, [$size['w'], $size['h']]);
        $fpdi->useTemplate($template);

        // $fpdi->SetTextColor(153,0,153);

        $width = $size['w'];
        $height = $size['h'];
        //$width = $fpdi->GetPageWidth();
        //$height = $fpdi->GetPageHeight();
        //$fpdi->SetX($width - 150);
        //$fpdi->SetY(-15);
        $fpdi->SetAutoPageBreak(false);

        $fontname = TCPDF_FONTS::addTTFfont(public_path('Cairo-Bold.ttf'), '', 'TrueTypeUnicode', 32);
        //$fpdi->AddFont('amiri','',public_path('Amiri-Bold.ttf'),true);
        $fpdi->SetFont($fontname, '', 10);
        //$text = "Enter text";
        $text = 'لَكِنْ َلا بُدَّ أَنْ أَوْضَحَ لَك أَنَّ كُلَّ هَذِهِ
اْلأَفْكَارِ الْمَغْلُوطَةِ حَوْلَ اسْتِنْكَارِ
النِّشْوَةِ وَتَمْجِيدِ اْلأَلَمِ نَشَأْت بِالْفِعْلِ،
وَسَأَعْرِضُ لَك التَّفَاصِيلَ لِتَكْتَشِفَ
حَقِيقَةً وَأَسَاسَ تِلْكَ السَّعَادَةِ الْبَشَرِيَّةِ،
فَلا أَحَدَ يَرْفُضُ أَوْ يُكْرَهُ أَوْ يَتَجَنَّبَ
الشُّعُورُ بِالسَّعَادَةِ، وَلَكِن';
        $text = 'لكن لا بد أن أوضح لك أن كل هذه الأفكار المغلوطة حول استنكار  النشوة وتمجيد الألم نشأت بالفعل، وسأعرض لك التفاصيل لتكتشف حقيقة وأساس تلك السعادة البشرية، فلا أحد يرفض أو يكره أو يتجنب الشعور بالسعادة، ولكن بفضل هؤلاء الأشخاص الذين لا يدركون بأن السعادة لا بد أن نستشعرها بصورة أكثر عقلانية ومنطقية فيعرضهم هذا لمواجهة الظروف الأليمة، وأكرر بأنه لا يوجد من يرغب في الحب ونيل المنال ويتلذذ بالآلام، الألم هو الألم ولكن نتيجة لظروف ما قد تكمن السعاده فيما نتحمله من كد وأسي.';
        //$fpdi->Text($left,$top,$text);
        //$content_height = $fpdi->GetMultiCellHeight(150, 5, $text, 0, 'C');
        //$fpdi->SetXY(0, $height - $content_height);
        $fpdi->SetXY(0, 0);

        $fpdi->Write(0, 'العربية', '', 0, 'L', true, 0, false, false, 0);
        //$fpdi->MultiCell(150, 5, utf8_decode($text), 0, 'L', false, false);
        //}

        return $fpdi->Output($outputFilePath, 'F');
    }

public function count($path)
{
    $pdf = file_get_contents($path);
    $number = preg_match_all("/\/Page\W/", $pdf, $dummy);

    return $number;
}
}
