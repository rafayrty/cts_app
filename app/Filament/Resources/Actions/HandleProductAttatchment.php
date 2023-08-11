<?php

namespace App\Filament\Resources\Actions;

use Closure;
use Illuminate\Support\Facades\Storage;
//use Spatie\PdfToImage\Pdf as ConvertToImage;
use Karkow\MuPdf\Pdf;
use setasign\Fpdi\Tcpdf\Fpdi;
use Illuminate\Support\Facades\Log;

class HandleProductAttatchment
{
    public $pdfs = [];

    //public $old_pdf_count = 0;
    public $old_pdf = '';

    public function __invoke(Closure $set, Closure $get, $state)
    {
        //$name = $state->getClientOriginalName();
        $name = uniqid().'.pdf';
        $this->old_pdf = $get('pdf_name');

        //Start by setting the pdfinfo and $pdfs
        // PDFINFO Stores information about the current files that we have their pdf images as well as dimensions and other info like name e.t.c
        $this->set_pdf_info($get, $set, $name);

        $this->add_to_pdf_info($get, $set, $state, $name);

        $set('../../pdf_info', json_encode($this->pdfs));

    }

    public function getDimensions($orientation = 'P',$document)
    {

//$pdf = new Fpdi($orientation,'pt'); // change the snd parameter to change the units
//$pdf->setSourceFile($document);
//$pageId = $pdf->importPage(1);
//$width = round($pdf->getPageWidth(),2);
//$height = round($pdf->getPageHeight(),2);
        $cmd = config('app.pdf_info_path');
        exec("$cmd \"$document\" 2>&1", $output);

        $dimensions = [];

        foreach ($output as $op) {
            //Extract the number

            //if (preg_match('~Page size:\s+([0-9\.]+) x ([0-9\.]+) pts \(rotated 0 degrees\)~', $op, $matches) === 1) {
            if (preg_match('~Page size:\s+([0-9\.]+) x ([0-9\.]+) pts \(rotated 0 degrees\)~', $op, $matches) === 1) {
                $dimensions['width'] = intval($matches[1]);
                $dimensions['height'] = intval($matches[2]);
                break;
            }

            if (preg_match('~Page size:\s+([0-9\.]+) x ([0-9\.]+) pts~', $op, $matches) === 1) {
                $dimensions['width'] = intval($matches[1]);
                $dimensions['height'] = intval($matches[2]);
                break;
            }
        }
        //$dimensions['width'] = $width;
        //$dimensions['height'] = $height;

        Log::info($dimensions);
        return $dimensions;
    }

    public function getPDFPages($document)
    {
        $cmd = config('app.pdf_info_path');
        exec("$cmd \"$document\" 2>&1", $output);
        $pagecount = 0;
        foreach ($output as $op) {
            // Extract the number
            if (preg_match("/Pages:\s*(\d+)/i", $op, $matches) === 1) {
                $pagecount = intval($matches[1]);
                break;
            }
        }

        return $pagecount;
    }

    public function getSplittedImages($img_path)
    {
        // Load the PNG image
        $image = imagecreatefrompng($img_path);

        // Get the width and height of the image
        $width = imagesx($image);
        $height = imagesy($image);

        // Create two new images for each half
        $leftImage = imagecreatetruecolor($width / 2, $height);
        $rightImage = imagecreatetruecolor($width / 2, $height);

        // Copy the left half of the original image to the left image
        imagecopy($leftImage, $image, 0, 0, 0, 0, $width / 2, $height);

        // Copy the right half of the original image to the right image
        imagecopy($rightImage, $image, 0, 0, $width / 2, 0, $width / 2, $height);

        // Save the two new images

        $left_path = 'uploads/'.time().'left.png';
        $right_path = 'uploads/'.time().'right.png';

        imagepng($leftImage, $left_path);
        imagepng($rightImage, $right_path);

        // Free up memory
        imagedestroy($image);
        imagedestroy($leftImage);
        imagedestroy($rightImage);

        return [$left_path, $right_path];
    }

    public function set_pdf_info(Closure $get, Closure $set, $name)
    {
        //Remove the existing document from pdfinfo if it exists
        if ($get('../../pdf_info') != '' && $get('pdf_name') != null) {
            $this->pdfs = json_decode($get('../../pdf_info'), true); // Map $this->pdfs from pdf_info
            $key = array_search($get('pdf_name'), array_column($this->pdfs, 'filename')); //Search using filename
            //$this->old_pdf_count = count($this->pdfs[$key]['pdf']);
            unset($this->pdfs[$key]); // Delete the entry from the array

            $set('../../pdf_info', json_encode($this->pdfs)); // Update the pdfinfo json field
        }        //Else proceed from scratch

        $set('pdf_name', $name);

        if ($get('../../pdf_info') != '') {
            $this->pdfs = json_decode($get('../../pdf_info'), true); // Map $this->pdfs from pdf_info it might have been updated as well
        } else {
            $this->pdfs = [];
        }
    }

    public function add_to_pdf_info(Closure $get, Closure $set, $state, $name): void
    {

        $mupdf = new Pdf($state->path(), config('app.mupdf_path'));

        $count = $this->getPDFPages($state->path()); //Get The Number of Pages

        //Incase we have a cover hard or soft
        if ($get('type') != 2) {

            $img_path = 'uploads/'.time().$get('type').'-'.'actual-cover'.'.png';

            $mupdf->setPage(1)->saveImage($img_path);

            $images = $this->getSplittedImages($img_path);


            unlink($img_path); //Delete the original cover image after getting the splitted version

            $dimensions = $this->getDimensions('L',$state->path());
            $set('dimensions', $dimensions);

            $dimensions['width'] = $dimensions['width'] / 2;

            if (app()->isProduction()) {
                $this->upload_to_do($images);
            }
            array_push($this->pdfs, [
                'filename' => $name,
                'dimensions' => json_encode($dimensions),
                'type' => $get('type'),
                'name' => $get('name'),
                'pdf' => $images,
            ]);

        } else {

            $dimensions = $this->getDimensions('P',$state->path());
            $set('dimensions', $dimensions);

            $images = [];
            for ($i = 1; $i <= $count; $i++) {
                $img_path = 'uploads/'.time().$get('type').'-'.$i.'.png';
                $mupdf->setPage($i)
                    ->saveImage($img_path);
                $images[] = $img_path;
            }

            //array_push($pdfs, ['filename' => $name, 'dimensions' => json_encode($get('dimensions')), 'type' => $get('type'), 'name' => $get('name'), 'pdf' => $images]);
            if (app()->isProduction()) {
                $this->upload_to_do($images);
            }
            array_push($this->pdfs, [
                'filename' => $name,
                'dimensions' => json_encode($dimensions),
                'type' => $get('type'),
                'name' => $get('name'),
                'pdf' => $images,
            ]);
        }

        $this->adjust_pages($get, $set, $count);
    }

    // Works in pdf reupload only
    //Incase the number of pages which were already assigned to the document are more than the new uploaded document
    public function adjust_pages(Closure $get, Closure $set, $new_count)
    {

        $pages = $get('../../pages');
        $barcodes = $get('../../barcodes');
        $dedications = $get('../../dedications');

        $new_pages = [];
        $new_barcodes = [];
        $new_dedications = [];

        if ($this->old_pdf) {

            foreach ($pages as &$page) {
                if ($page['document'] == $this->old_pdf) {
                    continue;
                }
                $new_pages[] = $page;
            }

            foreach ($barcodes as &$barcode) {
                if ($barcode['document'] == $this->old_pdf) {
                    continue;
                }
                $new_barcodes[] = $barcode;
            }

            if ($dedications) {

                foreach ($dedications as &$dedication) {
                    if ($dedication['document'] == $this->old_pdf) {
                        continue;
                    }
                    $new_dedications[] = $dedication;
                }

            }
            $set('../../pages', $new_pages);
            if ($new_dedications) {

                $set('../../dedications', $new_dedications);

            }
            $set('../../barcodes', $new_barcodes);

        }

    }

    public function upload_to_do($images)
    {

        foreach ($images as $image) {
            Storage::disk('do')->put($image, file_get_contents($image));
            Storage::disk('do')->setVisibility($image, 'public');
            // Delete the local file
            unlink($image);
        }
    }
}
