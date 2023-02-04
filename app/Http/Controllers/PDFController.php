<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use setasign\Fpdi\Fpdi;

class PDFController extends Controller
{
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
