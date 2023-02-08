<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use setasign\Fpdi\Fpdi;

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

        $fpdi = new Fpdi;

        $count = $fpdi->setSourceFile($file);

        $template = $fpdi->importPage(2);
        $size = $fpdi->getTemplateSize($template);

        $fpdi->AddPage($size['orientation'], [$size['width'], $size['height']]);
        $fpdi->useTemplate($template);

        $width = $size['width'];
        $height = $size['height'];
        $fpdi->SetFont('helvetica', '', $pages[0]['image']['predefined_texts'][0]['value']['font_size'] * 0.6);
        // $fpdi->SetTextColor(153,0,153);

        $fpdi->SetAutoPageBreak(false);
        $text = trim(html_entity_decode($pages[0]['image']['predefined_texts'][0]['value']['text']), " \t\n\r\0\x0B\xC2\xA0");
        //$fpdi->Text($left,$top,$text);
        $content_height = $fpdi->GetMultiCellHeight(150, 5, $text, 0, 'C');
        //$fpdi->SetXY(0, $height - $content_height);
        //$fpdi->SetXY($pages[0]['image']['predefined_texts'][0]['value']['X_coord'], 0);

        $color = $pages[0]['image']['predefined_texts'][0]['value']['color'];
        $fpdi->setTextColor(0, 255, 255);
        $fpdi->SetXY($pages[0]['image']['predefined_texts'][0]['value']['X_coord_percent'] / 100 * $width, $pages[0]['image']['predefined_texts'][0]['value']['Y_coord_percent'] / 100 * $height);
        $fpdi->MultiCell(($pages[0]['image']['predefined_texts'][0]['value']['max_width'] / 1000) * $width, 4.4, $text, 0, $pages[0]['image']['predefined_texts'][0]['value']['text_align'], false, false, 'rtl');
        //}

        $fpdi->Output($outputFilePath, 'F');

        return response()->file($outputFilePath);
    }

    public function mmToPx($px)
    {
        return $px * 18.5 / 72;
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
        $fpdi = new Fpdi;

        $count = $fpdi->setSourceFile($file);

        $template = $fpdi->importPage(1);
        $size = $fpdi->getTemplateSize($template);
        $fpdi->AddPage($size['orientation'], [$size['width'], $size['height']]);
        $fpdi->useTemplate($template);

        $fpdi->SetFont('helvetica', '', 10);
        // $fpdi->SetTextColor(153,0,153);

        $width = $size['width'];
        $height = $size['height'];
        //$width = $fpdi->GetPageWidth();
        //$height = $fpdi->GetPageHeight();
        //$fpdi->SetX($width - 150);
        //$fpdi->SetY(-15);
        $fpdi->SetAutoPageBreak(false);
        $text = 'continued from page 1. Yet more text. And more text. And more text.
 And more text. And more text. And more text. And more text. And more
 text. Oh, how boring typing this stuff. But not as boring as watching
 paint dry. And more text. And more text. And more text. And more text.
 Boring. More, a little more text. The end, and just as well. ';
        //$fpdi->Text($left,$top,$text);
        $content_height = $fpdi->GetMultiCellHeight(150, 5, $text, 0, 'C');
        $fpdi->SetXY(0, $height - $content_height);
        $fpdi->MultiCell(150, 5, $text, 0, 'L', false, false, 'rtl');
        //}

        return $fpdi->Output($outputFilePath, 'F');
    }
}
